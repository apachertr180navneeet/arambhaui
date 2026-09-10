/* ==========================================================================
   REACTIVE STATE MANAGER & CONNECTED ERP BUSINESS LOGIC
   GarmentERP - FashionWorks Pvt. Ltd.
   Full Live Backend API Integration
   ========================================================================== */

class ERPStateManager {
  constructor() {
    this.STORAGE_KEY = 'garment_erp_live_data_v2';
    this.listeners = [];
    this.isSyncing = false;
    this.data = JSON.parse(JSON.stringify(INITIAL_DATA));
    this.loadState();
  }

  // Load state from localStorage if available, then sync live backend data
  loadState() {
    try {
      const saved = localStorage.getItem(this.STORAGE_KEY);
      if (saved) {
        const parsed = JSON.parse(saved);
        this.data = Object.assign(JSON.parse(JSON.stringify(INITIAL_DATA)), parsed);
      }
    } catch (e) {
      console.warn("Could not load from localStorage:", e);
    }
  }

  saveState() {
    try {
      localStorage.setItem(this.STORAGE_KEY, JSON.stringify(this.data));
    } catch (e) {
      console.error("Could not save to localStorage:", e);
    }
    this.notify();
  }

  resetState() {
    this.data = JSON.parse(JSON.stringify(INITIAL_DATA));
    this.saveState();
    this.syncWithBackend();
  }

  subscribe(listener) {
    this.listeners.push(listener);
    return () => {
      this.listeners = this.listeners.filter(l => l !== listener);
    };
  }

  notify() {
    this.listeners.forEach(fn => {
      try {
        fn(this.data);
      } catch (err) {
        console.error("Listener error:", err);
      }
    });
  }

  // CSRF Token Helper
  getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
  }

  // Audit Logging Helper
  logActivity(action, module, record, user = (this.data.currentUser ? this.data.currentUser.name : "Admin User")) {
    const newLog = {
      id: `LOG-${String((this.data.activityLogs || []).length + 1).padStart(3, '0')}`,
      user: user,
      role: (this.data.currentUser ? this.data.currentUser.role : "Administrator"),
      action: action,
      module: module,
      record: record,
      time: "Just now",
      ip: "192.168.1." + Math.floor(Math.random() * 80 + 10)
    };
    if (!this.data.activityLogs) this.data.activityLogs = [];
    this.data.activityLogs.unshift(newLog);
    if (this.data.activityLogs.length > 50) {
      this.data.activityLogs.pop();
    }
    this.saveState();
  }

  // =========================================================================
  // LIVE BACKEND API SYNCHRONIZATION
  // =========================================================================
  async syncWithBackend() {
    if (this.isSyncing) return;
    this.isSyncing = true;

    const headers = {
      'Accept': 'application/json',
      'X-Requested-With': 'XMLHttpRequest'
    };

    const fetchSafe = async (url) => {
      try {
        const res = await fetch(url, { headers });
        if (!res.ok) return null;
        return await res.json();
      } catch (e) {
        console.warn(`Fetch error for ${url}:`, e);
        return null;
      }
    };

    try {
      const [
        customers,
        vendors,
        jobWorkers,
        items,
        units,
        jobAssignments,
        productionOrders,
        qualityChecks,
        lotTracking,
        qrVouchers,
        dispatchChallans,
        invoices,
        users,
        activityLogs,
        settings
      ] = await Promise.all([
        fetchSafe('/masters/customers'),
        fetchSafe('/masters/vendors'),
        fetchSafe('/masters/jobworkers'),
        fetchSafe('/masters/items'),
        fetchSafe('/masters/units'),
        fetchSafe('/jobwork/assign'),
        fetchSafe('/production/orders'),
        fetchSafe('/production/qc'),
        fetchSafe('/production/tracking'),
        fetchSafe('/qr/history'),
        fetchSafe('/dispatch/challans'),
        fetchSafe('/invoices/list'),
        fetchSafe('/admin/users'),
        fetchSafe('/admin/activity'),
        fetchSafe('/admin/settings')
      ]);

      if (Array.isArray(customers)) {
        this.data.customers = customers.map(c => ({
          id: c.code || `CUST-${String(c.id).padStart(3, '0')}`,
          dbId: c.id,
          code: c.code || `CUST-${String(c.id).padStart(3, '0')}`,
          name: c.name,
          companyName: c.company_name || c.name,
          contactPerson: c.contact_person || "",
          mobile: c.phone || "",
          phone: c.phone || "",
          email: c.email || "",
          gstin: c.gstin || "",
          address: c.address || "",
          city: c.city || "Mumbai",
          state: c.state || "Maharashtra",
          creditLimit: Number(c.credit_limit || 2500000),
          paymentTerms: c.payment_terms || "30 Days",
          outstanding: Number(c.outstanding || 0),
          totalOrders: Number(c.production_orders_count || c.invoices_count || 0),
          createdAt: c.created_at ? c.created_at.split('T')[0] : new Date().toISOString().split('T')[0],
          status: c.status || "Active"
        }));
      }

      if (Array.isArray(vendors)) {
        this.data.vendors = vendors.map(v => ({
          id: v.code || `VND-${String(v.id).padStart(3, '0')}`,
          dbId: v.id,
          code: v.code || `VND-${String(v.id).padStart(3, '0')}`,
          name: v.name,
          companyName: v.company_name || v.name,
          contactPerson: v.contact_person || v.name,
          mobile: v.phone || "",
          phone: v.phone || "",
          email: v.email || "",
          gstin: v.gstin || "",
          category: v.category || "Fabric Supplier",
          address: v.address || "",
          city: v.city || "Surat",
          state: v.state || "Gujarat",
          paymentTerms: (v.credit_days || 30) + " Days",
          creditDays: Number(v.credit_days || 30),
          outstanding: Number(v.outstanding || 0),
          status: v.status || "Active"
        }));
      }

      if (Array.isArray(jobWorkers)) {
        this.data.jobWorkers = jobWorkers.map(j => ({
          id: j.code || `JWK-${String(j.id).padStart(3, '0')}`,
          dbId: j.id,
          code: j.code || `JWK-${String(j.id).padStart(3, '0')}`,
          name: j.name,
          contactPerson: j.name,
          mobile: j.phone || "",
          phone: j.phone || "",
          process: j.skill_type || "Stitching",
          rate: Number(j.rate_per_piece || 20),
          rateUnit: "Piece",
          city: j.city || "Mumbai",
          capacityPerDay: Number(j.daily_capacity || 1000),
          outstanding: Number(j.outstanding || 0),
          address: j.address || "",
          status: j.status || "Active"
        }));
      }

      if (Array.isArray(items)) {
        this.data.items = items.map(i => ({
          id: i.code || `ITM-${String(i.id).padStart(3, '0')}`,
          dbId: i.id,
          code: i.code || `ITM-${String(i.id).padStart(3, '0')}`,
          name: i.name,
          type: i.type || (i.category === 'Fabric' ? 'Raw Material' : (i.category === 'Trims' ? 'Accessories' : (i.category === 'Packing' ? 'Packaging' : 'Finished Goods'))),
          category: i.category || "Fabric",
          unit: i.unit || "Meters",
          brand: i.brand || "FashionWorks",
          fabric: i.fabric || (i.category === 'Fabric' ? 'Cotton' : 'N/A'),
          color: i.color || "Assorted",
          size: i.size || "Standard",
          hsn: i.hsn_code || "5208",
          rate: Number(i.unit_cost || 0),
          unitCost: Number(i.unit_cost || 0),
          currentStock: Number(i.current_stock || 0),
          openingStock: Number(i.current_stock || 0),
          minStock: Number(i.min_stock || 100),
          reorderLevel: Number(i.min_stock || 100),
          location: i.location || "Zone A",
          status: i.status || "Active"
        }));
      }

      if (Array.isArray(units)) {
        this.data.units = units.map(u => ({
          id: u.id,
          dbId: u.id,
          code: u.code || u.name,
          name: u.name,
          parentId: u.parent_id || null,
          parentName: u.parent ? u.parent.name : null,
          conversionFactor: u.conversion_factor !== null && u.conversion_factor !== undefined ? Number(u.conversion_factor) : null,
          symbol: u.symbol || "",
          decimalPlaces: Number(u.decimal_places ?? 2),
          description: u.description || "",
          status: u.status || "Active"
        }));
      }

      if (Array.isArray(jobAssignments)) {
        this.data.jobWorks = jobAssignments.map(ja => ({
          id: ja.job_order_no || `JW-2026-${String(ja.id).padStart(4, '0')}`,
          dbId: ja.id,
          assignNo: ja.job_order_no,
          date: ja.issue_date,
          orderNo: ja.lot_number || "",
          customer: "Garment Production",
          item: ja.style_name,
          process: ja.process_name,
          jobWorker: ja.job_worker_name,
          freight: "To Pay",
          lotNo: ja.lot_number,
          stage: ja.process_name + " Stage",
          quantity: Number(ja.issued_qty),
          sentQty: Number(ja.issued_qty),
          receivedGoodQty: ja.status === 'Completed' ? Number(ja.issued_qty) : 0,
          pendingQty: ja.status === 'Completed' ? 0 : Number(ja.issued_qty),
          rate: Number(ja.rate_per_piece || 20),
          totalAmount: Number(ja.total_amount || (ja.issued_qty * (ja.rate_per_piece || 20))),
          outwardDate: ja.issue_date,
          expectedReturnDate: ja.due_date || ja.issue_date,
          status: ja.status === 'Issued' ? 'In Progress' : (ja.status || 'In Progress'),
          qcStatus: ja.status === 'Completed' ? 'QC Passed' : 'Pending QC',
          items: (ja.items || []).map(it => ({
            item: ja.style_name,
            lotNo: ja.lot_number,
            size: it.size,
            color: it.color,
            qty: it.qty,
            rate: Number(ja.rate_per_piece || 20),
            amount: Number(it.qty * (ja.rate_per_piece || 20))
          }))
        }));
      }

      if (Array.isArray(productionOrders)) {
        this.data.productionOrders = productionOrders.map(po => ({
          id: po.order_no || `ORD-2026-${String(po.id).padStart(3, '0')}`,
          dbId: po.id,
          orderNo: po.order_no,
          customer: po.customer_name,
          product: po.style_name,
          itemCode: po.style_name,
          quantity: Number(po.order_qty),
          rate: Number(po.unit_price),
          amount: Number(po.total_amount || (po.order_qty * po.unit_price)),
          orderDate: po.order_date,
          deliveryDate: po.delivery_date,
          stage: po.current_stage || "Cutting",
          progress: Number(po.progress_percent || 20),
          status: po.status || "Scheduled",
          priority: "Normal"
        }));
        this.data.salesOrders = this.data.productionOrders;
      }

      if (Array.isArray(qualityChecks)) {
        this.data.qualityChecks = qualityChecks.map(qc => ({
          id: qc.qc_batch_no || `QC-2026-${String(qc.id).padStart(3, '0')}`,
          dbId: qc.id,
          orderNo: qc.order_no,
          lotNumber: qc.lot_number,
          styleName: qc.style_name,
          date: qc.inspection_date,
          inspector: qc.inspector_name,
          inspectedQty: Number(qc.total_inspected),
          passedQty: Number(qc.passed_qty),
          minorDefects: Number(qc.minor_defects || 0),
          majorDefects: Number(qc.major_defects || 0),
          defectPercent: Number(qc.defect_percent || 0),
          status: qc.status || "Passed",
          remarks: qc.remarks || ""
        }));
      }

      if (Array.isArray(lotTracking)) {
        this.data.lotTracking = lotTracking.map(lt => ({
          lotNo: lt.lot_number,
          dbId: lt.id,
          orderNo: lt.order_no,
          styleName: lt.style_name,
          initialQty: Number(lt.initial_qty),
          currentQty: Number(lt.current_qty),
          currentStage: lt.current_stage,
          location: lt.current_location,
          status: lt.status
        }));
      }

      if (Array.isArray(qrVouchers)) {
        this.data.qrVouchers = qrVouchers.map(q => ({
          id: q.voucher_code,
          dbId: q.id,
          code: q.voucher_code,
          customerName: q.customer_name,
          discountPercent: Number(q.discount_percent),
          maxDiscountCap: Number(q.max_discount_cap),
          minOrderValue: Number(q.min_order_value),
          validFrom: q.valid_from,
          validUntil: q.valid_until,
          isRedeemed: Boolean(q.is_redeemed),
          status: q.status || "Active"
        }));
        this.data.discountCoupons = this.data.qrVouchers;
      }

      if (Array.isArray(dispatchChallans)) {
        this.data.dispatchChallans = dispatchChallans.map(dc => ({
          id: dc.challan_no || `DC-2026-${String(dc.id).padStart(3, '0')}`,
          dbId: dc.id,
          challanNo: dc.challan_no,
          orderNo: dc.order_no,
          customer: dc.customer_name,
          dispatchDate: dc.dispatch_date,
          transporter: dc.transporter_name,
          lrNumber: dc.lr_number || "",
          vehicleNumber: dc.vehicle_number || "",
          destination: dc.destination_city || "Mumbai",
          totalCartons: Number(dc.total_cartons),
          totalQty: Number(dc.total_qty),
          status: dc.status || "In Transit"
        }));
      }

      if (Array.isArray(invoices)) {
        this.data.invoices = invoices.map(inv => ({
          id: inv.invoice_no || `INV-2026-${String(inv.id).padStart(3, '0')}`,
          dbId: inv.id,
          invoiceNo: inv.invoice_no,
          orderNo: inv.order_no || "",
          customer: inv.customer_name,
          companyName: inv.company_name || inv.customer_name,
          invoiceDate: inv.invoice_date,
          dueDate: inv.due_date,
          subtotal: Number(inv.subtotal || 0),
          discountAmount: Number(inv.discount_amount || 0),
          voucherCode: inv.voucher_code || "",
          taxableAmount: Number(inv.taxable_amount || inv.subtotal || 0),
          cgstAmount: Number(inv.cgst_amount || 0),
          sgstAmount: Number(inv.sgst_amount || 0),
          amount: Number(inv.grand_total || 0),
          grandTotal: Number(inv.grand_total || 0),
          paidAmount: Number(inv.paid_amount || 0),
          balanceAmount: Number(inv.balance_due || inv.grand_total || 0),
          status: inv.status || "Sent",
          items: (inv.items || []).map(it => ({
            description: it.item_description,
            qty: Number(it.qty),
            rate: Number(it.rate),
            amount: Number(it.amount)
          }))
        }));
      }

      if (Array.isArray(users)) {
        this.data.users = users.map(u => ({
          id: `USR-${String(u.id).padStart(3, '0')}`,
          dbId: u.id,
          name: u.name,
          email: u.email,
          role: u.role || "Administrator",
          status: u.status || "Active",
          lastLogin: "Active Now"
        }));
      }

      if (Array.isArray(activityLogs) && activityLogs.length > 0) {
        this.data.activityLogs = activityLogs.map(l => ({
          id: `LOG-${String(l.id).padStart(3, '0')}`,
          user: l.user_name || "System Administrator",
          role: "Administrator",
          action: l.action,
          module: l.module,
          record: l.description,
          time: l.created_at ? l.created_at.split('T')[0] : "Recently",
          ip: "192.168.1.1"
        }));
      }

      if (settings && typeof settings === 'object') {
        this.data.company = Object.assign(this.data.company, settings);
      }

      this.data.dispatches = this.data.dispatchChallans || [];
      this.data.lots = this.data.lotTracking || [];

      this.saveState();
      console.log("ERP Backend sync complete. Live datasets loaded.");
    } catch (e) {
      console.error("ERP Backend sync encountered an error:", e);
    } finally {
      this.isSyncing = false;
      this.notify();
    }
  }

  // Dashboard KPI Computation Helper
  getDashboardStats() {
    const orders = this.data.productionOrders || this.data.salesOrders || [];
    const dispatches = this.data.dispatchChallans || this.data.dispatches || [];
    const inwards = this.data.purchaseInwards || [];
    const jw = this.data.jobAssignments || this.data.jobWorks || [];
    const items = this.data.items || [];
    const rawStock = items.filter(i => (i.category || '').toLowerCase().includes('fabric') || (i.category || '').toLowerCase().includes('raw')).reduce((sum, i) => sum + (Number(i.stock) || Number(i.current_stock) || 0), 0) || 5000;
    const wipStock = jw.reduce((sum, j) => sum + (Number(j.quantity) || 0), 0) || 2500;
    const finishedStock = orders.filter(o => o.status === "Ready for Dispatch" || o.stage === "Ready").reduce((sum, o) => sum + (Number(o.quantity) || 0), 0) || 1200;
    const readyForDispatch = orders.filter(o => o.status === "Ready for Dispatch" || o.stage === "Ready").length;
    const inProduction = orders.filter(o => o.status === "In Production" || o.status === "Scheduled").length;

    return {
      totalOrders: orders.length,
      readyForDispatch,
      inProduction,
      totalDispatches: dispatches.length,
      totalInwards: inwards.length,
      activeJobWorks: jw.filter(j => j.status !== 'Completed').length,
      rawStock,
      wipStock,
      finishedStock
    };
  }

  getCustomerStats() {
    const customers = this.data.customers || [];
    const total = customers.length;
    const active = customers.filter(c => c.status === 'Active').length;
    const totalOutstanding = customers.reduce((sum, c) => sum + (Number(c.outstanding) || 0), 0);
    const totalCreditLimit = customers.reduce((sum, c) => sum + (Number(c.creditLimit) || 0), 0);
    const creditUtilization = totalCreditLimit > 0 ? ((totalOutstanding / totalCreditLimit) * 100).toFixed(1) : "0";
    const highRisk = customers.filter(c => ((Number(c.outstanding) || 0) >= (Number(c.creditLimit) || 0) * 0.9) && ((Number(c.outstanding) || 0) > 0)).length;
    const cities = Array.from(new Set(customers.map(c => c.city).filter(Boolean)));

    return {
      total,
      active,
      totalOutstanding,
      totalCreditLimit,
      creditUtilization,
      highRisk,
      cities
    };
  }

  getItemStats() {
    const items = this.data.items || [];
    const total = items.length;
    const active = items.filter(i => (i.status || 'Active') === 'Active').length;
    const totalStock = items.reduce((sum, i) => sum + (Number(i.currentStock) || 0), 0);
    const totalValuation = items.reduce((sum, i) => sum + ((Number(i.currentStock) || 0) * (Number(i.rate || i.unitCost) || 0)), 0);
    const lowStockCount = items.filter(i => (Number(i.currentStock) || 0) <= (Number(i.minStock || i.reorderLevel) || 100)).length;
    const categories = Array.from(new Set(items.map(i => i.category).filter(Boolean)));
    const types = Array.from(new Set(items.map(i => i.type).filter(Boolean)));

    return {
      total,
      active,
      totalStock,
      totalValuation,
      lowStockCount,
      categories,
      types
    };
  }

  // =========================================================================
  // 1. CUSTOMER MASTER CRUD & METHODS
  // =========================================================================
  addCustomer(customer) {
    const nextNum = (this.data.customers || []).length + 1;
    const id = customer.id || `CUST-${String(nextNum).padStart(3, '0')}`;
    const newCust = {
      id,
      code: id,
      name: customer.name || "",
      companyName: customer.companyName || customer.company_name || customer.name || "",
      contactPerson: customer.contactPerson || customer.contact_person || "",
      mobile: customer.mobile || customer.phone || "",
      phone: customer.phone || customer.mobile || "",
      email: customer.email || "",
      gstin: (customer.gstin || "").toUpperCase(),
      address: customer.address || "",
      city: customer.city || "Mumbai",
      state: customer.state || "Maharashtra",
      pincode: customer.pincode || "400013",
      creditLimit: Number(customer.creditLimit || customer.credit_limit || 2500000),
      paymentTerms: customer.paymentTerms || customer.payment_terms || "30 Days",
      outstanding: Number(customer.outstanding || customer.openingBalance || 0),
      totalOrders: Number(customer.totalOrders || 0),
      createdAt: customer.createdAt || new Date().toISOString().split('T')[0],
      status: customer.status || "Active"
    };

    if (!this.data.customers) this.data.customers = [];
    this.data.customers.unshift(newCust);
    this.logActivity(`Added customer: ${newCust.name} (${id})`, "Customer Master", id);
    this.saveState();

    // Async sync with Laravel Backend
    try {
      fetch('/masters/customers', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': this.getCsrfToken(),
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
          name: newCust.name,
          company_name: newCust.companyName,
          contact_person: newCust.contactPerson,
          phone: newCust.phone || newCust.mobile,
          email: newCust.email,
          gstin: newCust.gstin,
          address: newCust.address,
          city: newCust.city,
          state: newCust.state,
          credit_limit: newCust.creditLimit,
          outstanding: newCust.outstanding,
          payment_terms: newCust.paymentTerms,
          status: newCust.status
        })
      }).then(r => r.json()).then(res => {
        if (res && res.customer && res.customer.id) {
          newCust.dbId = res.customer.id;
        }
      }).catch(err => console.warn("API sync note:", err));
    } catch (e) {
      console.warn("API dispatch error:", e);
    }

    return newCust;
  }

  updateCustomer(id, updatedData) {
    const idx = (this.data.customers || []).findIndex(c => c.id === id || c.code === id || c.name === id);
    if (idx !== -1) {
      const current = this.data.customers[idx];
      const merged = {
        ...current,
        ...updatedData,
        companyName: updatedData.companyName || updatedData.company_name || current.companyName,
        contactPerson: updatedData.contactPerson || updatedData.contact_person || current.contactPerson,
        mobile: updatedData.mobile || updatedData.phone || current.mobile,
        phone: updatedData.phone || updatedData.mobile || current.phone,
        creditLimit: updatedData.creditLimit !== undefined ? Number(updatedData.creditLimit) : current.creditLimit,
        outstanding: updatedData.outstanding !== undefined ? Number(updatedData.outstanding) : current.outstanding
      };
      this.data.customers[idx] = merged;
      this.logActivity(`Updated customer details: ${merged.name} (${id})`, "Customer Master", id);
      this.saveState();

      if (current.dbId) {
        fetch(`/masters/customers/${current.dbId}`, {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': this.getCsrfToken(),
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: JSON.stringify({
            name: merged.name,
            company_name: merged.companyName,
            contact_person: merged.contactPerson,
            phone: merged.phone || merged.mobile,
            email: merged.email,
            gstin: merged.gstin,
            address: merged.address,
            city: merged.city,
            state: merged.state,
            credit_limit: merged.creditLimit,
            outstanding: merged.outstanding,
            payment_terms: merged.paymentTerms,
            status: merged.status
          })
        }).catch(err => console.warn("API update note:", err));
      }
      return merged;
    }
  }

  deleteCustomer(id) {
    const cust = (this.data.customers || []).find(c => c.id === id || c.code === id);
    if (cust) {
      this.data.customers = this.data.customers.filter(c => c.id !== id && c.code !== id);
      this.logActivity(`Deleted customer: ${cust.name} (${id})`, "Customer Master", id);
      this.saveState();

      if (cust.dbId) {
        fetch(`/masters/customers/${cust.dbId}`, {
          method: 'DELETE',
          headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': this.getCsrfToken(),
            'X-Requested-With': 'XMLHttpRequest'
          }
        }).catch(err => console.warn("API delete note:", err));
      }
    }
  }

  getCustomerById(id) {
    if (!id) return null;
    return (this.data.customers || []).find(c => c.id === id || c.code === id || c.name.toLowerCase() === id.toLowerCase());
  }

  getCustomerStats() {
    const custs = this.data.customers || [];
    const total = custs.length;
    const active = custs.filter(c => c.status === 'Active').length;
    const totalReceivable = custs.reduce((sum, c) => sum + (Number(c.outstanding) || 0), 0);
    const totalCreditGiven = custs.reduce((sum, c) => sum + (Number(c.creditLimit) || 0), 0);
    const overdueAccounts = custs.filter(c => (Number(c.outstanding) || 0) > 0).length;

    return {
      total,
      active,
      totalReceivable,
      totalCreditGiven,
      overdueAccounts,
      creditUtilization: totalCreditGiven > 0 ? ((totalReceivable / totalCreditGiven) * 100).toFixed(1) : 0
    };
  }

  getCustomerStatement(customerId) {
    const cust = this.getCustomerById(customerId);
    if (!cust) return null;

    const custName = (cust.name || '').trim().toLowerCase();
    const custCode = (cust.id || cust.code || '').trim().toLowerCase();

    const invoices = (this.data.invoices || []).filter(i => {
      const c = (i.customer || '').trim().toLowerCase();
      const code = (i.customerId || '').trim().toLowerCase();
      return c === custName || (custCode && code === custCode);
    });

    const orders = (this.data.productionOrders || []).filter(o => {
      const c = (o.customer || '').trim().toLowerCase();
      return c === custName;
    });

    const payments = (this.data.payments || []).filter(p => {
      const c = (p.customer || '').trim().toLowerCase();
      return c === custName;
    });

    const totalInvoiced = invoices.reduce((sum, i) => sum + (Number(i.amount || i.grandTotal || i.totalAmount) || 0), 0);
    const totalReceived = payments.reduce((sum, p) => sum + (Number(p.amount) || 0), 0);
    const totalPaid = totalReceived;

    // Build unified chronological ledger
    const ledger = [];

    invoices.forEach(inv => {
      const amt = Number(inv.grandTotal || inv.amount || inv.totalAmount || 0);
      ledger.push({
        date: inv.date || inv.invoiceDate || "2026-09-01",
        type: "Tax Invoice",
        description: `Tax Invoice ${inv.invoiceNo || inv.id}`,
        ref: inv.invoiceNo || inv.id,
        debit: amt,
        credit: 0
      });
    });

    payments.forEach(pay => {
      const amt = Number(pay.amount || 0);
      ledger.push({
        date: pay.date || pay.paymentDate || "2026-09-01",
        type: "Payment Receipt",
        description: `Payment via ${pay.mode || 'Bank'} (${pay.refNo || pay.reference || 'Direct'})`,
        ref: pay.refNo || pay.receiptNo || "RCP-001",
        debit: 0,
        credit: amt
      });
    });

    // If no explicit transactions but customer has outstanding balance:
    if (ledger.length === 0 && Number(cust.outstanding || 0) > 0) {
      ledger.push({
        date: "2026-09-01",
        type: "Opening Balance",
        description: "Initial ledger balance brought forward",
        ref: "OB-2026",
        debit: Number(cust.outstanding),
        credit: 0
      });
    }

    // Sort chronologically
    ledger.sort((a, b) => new Date(a.date) - new Date(b.date));

    // Compute running balance
    let runningBalance = 0;
    ledger.forEach(row => {
      runningBalance += (row.debit - row.credit);
      row.balance = runningBalance;
    });

    return {
      customer: cust,
      invoices,
      orders,
      payments,
      ledger,
      totalInvoiced: totalInvoiced || (ledger.length > 0 ? ledger.reduce((s, r) => s + r.debit, 0) : Number(cust.outstanding || 0)),
      totalReceived,
      totalPaid,
      outstanding: Number(cust.outstanding || runningBalance || 0),
      availableCredit: Math.max(0, (cust.creditLimit || 0) - (cust.outstanding || 0))
    };
  }

  // =========================================================================
  // 2. VENDOR MASTER CRUD
  // =========================================================================
  addVendor(vendor) {
    const nextNum = (this.data.vendors || []).length + 1;
    const id = vendor.id || `VND-${String(nextNum).padStart(3, '0')}`;
    const newVendor = {
      id,
      code: id,
      name: vendor.name || "",
      companyName: vendor.companyName || vendor.name,
      contactPerson: vendor.contactPerson || vendor.name,
      mobile: vendor.mobile || vendor.phone || "",
      phone: vendor.phone || vendor.mobile || "",
      email: vendor.email || "",
      gstin: (vendor.gstin || "").toUpperCase(),
      category: vendor.category || "Fabric Supplier",
      city: vendor.city || "Surat",
      state: vendor.state || "Gujarat",
      paymentTerms: vendor.paymentTerms || "30 Days",
      creditDays: Number(vendor.creditDays || 30),
      outstanding: Number(vendor.outstanding || 0),
      status: vendor.status || "Active",
      address: vendor.address || ""
    };

    if (!this.data.vendors) this.data.vendors = [];
    this.data.vendors.unshift(newVendor);
    this.logActivity(`Added vendor: ${newVendor.name} (${id})`, "Vendor Master", id);
    this.saveState();

    fetch('/masters/vendors', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': this.getCsrfToken(),
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify({
        name: newVendor.name,
        company_name: newVendor.companyName,
        phone: newVendor.phone,
        email: newVendor.email,
        gstin: newVendor.gstin,
        category: newVendor.category,
        address: newVendor.address,
        credit_days: newVendor.creditDays
      })
    }).then(r => r.json()).then(res => {
      if (res && res.vendor && res.vendor.id) newVendor.dbId = res.vendor.id;
    }).catch(e => console.warn("Vendor sync note:", e));

    return newVendor;
  }

  updateVendor(id, updatedData) {
    const idx = (this.data.vendors || []).findIndex(v => v.id === id || v.code === id || v.name === id);
    if (idx !== -1) {
      const current = this.data.vendors[idx];
      this.data.vendors[idx] = { ...current, ...updatedData };
      this.logActivity(`Updated vendor: ${this.data.vendors[idx].name}`, "Vendor Master", id);
      this.saveState();

      if (current.dbId) {
        fetch(`/masters/vendors/${current.dbId}`, {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': this.getCsrfToken(),
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: JSON.stringify(updatedData)
        }).catch(e => console.warn("Vendor update note:", e));
      }
    }
  }

  deleteVendor(id) {
    const v = (this.data.vendors || []).find(v => v.id === id || v.code === id);
    if (v) {
      this.data.vendors = this.data.vendors.filter(item => item.id !== id && item.code !== id);
      this.logActivity(`Deleted vendor: ${v.name}`, "Vendor Master", id);
      this.saveState();

      if (v.dbId) {
        fetch(`/masters/vendors/${v.dbId}`, {
          method: 'DELETE',
          headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': this.getCsrfToken(),
            'X-Requested-With': 'XMLHttpRequest'
          }
        }).catch(e => console.warn("Vendor delete note:", e));
      }
    }
  }

  getVendorById(id) {
    return (this.data.vendors || []).find(v => v.id === id || v.code === id || v.name.toLowerCase() === (id || '').toLowerCase());
  }

  // =========================================================================
  // 3. JOB WORKER MASTER CRUD
  // =========================================================================
  addJobWorker(jw) {
    const nextNum = (this.data.jobWorkers || []).length + 1;
    const id = jw.id || `JWK-${String(nextNum).padStart(3, '0')}`;
    const newJw = {
      id,
      code: id,
      name: jw.name || "",
      contactPerson: jw.contactPerson || jw.name,
      mobile: jw.mobile || jw.phone || "",
      phone: jw.phone || jw.mobile || "",
      process: jw.process || "Stitching",
      rate: Number(jw.rate || 20),
      rateUnit: "Piece",
      city: jw.city || "Mumbai",
      capacityPerDay: Number(jw.capacityPerDay || 1000),
      outstanding: Number(jw.outstanding || 0),
      status: jw.status || "Active",
      address: jw.address || ""
    };

    if (!this.data.jobWorkers) this.data.jobWorkers = [];
    this.data.jobWorkers.unshift(newJw);
    this.logActivity(`Added job worker: ${newJw.name} (${id})`, "Job Worker Master", id);
    this.saveState();

    fetch('/masters/jobworkers', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': this.getCsrfToken(),
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify({
        name: newJw.name,
        phone: newJw.phone,
        skill_type: newJw.process,
        rate_per_piece: newJw.rate,
        daily_capacity: newJw.capacityPerDay,
        address: newJw.address
      })
    }).then(r => r.json()).then(res => {
      if (res && res.job_worker && res.job_worker.id) newJw.dbId = res.job_worker.id;
    }).catch(e => console.warn("JW sync note:", e));

    return newJw;
  }

  updateJobWorker(id, updatedData) {
    const idx = (this.data.jobWorkers || []).findIndex(j => j.id === id || j.code === id || j.name === id);
    if (idx !== -1) {
      const current = this.data.jobWorkers[idx];
      this.data.jobWorkers[idx] = { ...current, ...updatedData };
      this.logActivity(`Updated job worker: ${this.data.jobWorkers[idx].name}`, "Job Worker Master", id);
      this.saveState();

      if (current.dbId) {
        fetch(`/masters/jobworkers/${current.dbId}`, {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': this.getCsrfToken(),
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: JSON.stringify(updatedData)
        }).catch(e => console.warn("JW update note:", e));
      }
    }
  }

  deleteJobWorker(id) {
    const j = (this.data.jobWorkers || []).find(j => j.id === id || j.code === id);
    if (j) {
      this.data.jobWorkers = this.data.jobWorkers.filter(item => item.id !== id && item.code !== id);
      this.logActivity(`Deleted job worker: ${j.name}`, "Job Worker Master", id);
      this.saveState();

      if (j.dbId) {
        fetch(`/masters/jobworkers/${j.dbId}`, {
          method: 'DELETE',
          headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': this.getCsrfToken(),
            'X-Requested-With': 'XMLHttpRequest'
          }
        }).catch(e => console.warn("JW delete note:", e));
      }
    }
  }

  getJobWorkerById(id) {
    return (this.data.jobWorkers || []).find(j => j.id === id || j.code === id || j.name.toLowerCase() === (id || '').toLowerCase());
  }

  // =========================================================================
  // 4. ITEM MASTER CRUD
  // =========================================================================
  async addItem(item) {
    const nextNum = (this.data.items || []).length + 1;
    const code = (item.code || `ITM-${String(nextNum).padStart(3, '0')}`).trim();
    const rate = Number(item.rate !== undefined ? item.rate : (item.unitCost !== undefined ? item.unitCost : 0));
    const currentStock = Number(item.currentStock !== undefined ? item.currentStock : (item.openingStock !== undefined ? item.openingStock : 0));
    const minStock = Number(item.minStock !== undefined ? item.minStock : (item.reorderLevel !== undefined ? item.reorderLevel : 100));

    const newItem = {
      id: code,
      dbId: null,
      code: code,
      name: (item.name || "").trim(),
      type: item.type || "Raw Material",
      category: item.category || "Fabric",
      unit: item.unit || "Pieces",
      brand: item.brand || "FashionWorks",
      fabric: item.fabric || "100% Cotton",
      color: item.color || "Standard",
      size: item.size || "Free",
      hsn: item.hsn || item.hsn_code || "5208",
      rate: rate,
      unitCost: rate,
      currentStock: currentStock,
      openingStock: currentStock,
      minStock: minStock,
      reorderLevel: minStock,
      location: item.location || "Zone A",
      status: item.status || "Active"
    };

    if (!this.data.items) this.data.items = [];
    this.data.items.unshift(newItem);
    this.logActivity(`Added item: ${newItem.name} (${newItem.code})`, "Item Master", newItem.code);
    this.saveState();

    try {
      const response = await fetch('/masters/items', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': this.getCsrfToken(),
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
          code: newItem.code,
          name: newItem.name,
          type: newItem.type,
          category: newItem.category,
          brand: newItem.brand,
          fabric: newItem.fabric,
          color: newItem.color,
          size: newItem.size,
          unit: newItem.unit,
          unit_cost: newItem.unitCost,
          current_stock: newItem.currentStock,
          min_stock: newItem.minStock,
          hsn_code: newItem.hsn,
          location: newItem.location,
          status: newItem.status
        })
      });
      const res = await response.json();
      if (res && res.item && res.item.id) {
        newItem.dbId = res.item.id;
        if (res.item.code) {
          newItem.code = res.item.code;
          newItem.id = res.item.code;
        }
        this.saveState();
      }
    } catch (e) {
      console.warn("Item sync note:", e);
    }

    return newItem;
  }

  async updateItem(id, updatedData) {
    const idx = (this.data.items || []).findIndex(i => i.id === id || i.code === id || (i.dbId && String(i.dbId) === String(id)) || i.name === id);
    if (idx !== -1) {
      const current = this.data.items[idx];
      const rate = updatedData.rate !== undefined ? Number(updatedData.rate) : (updatedData.unitCost !== undefined ? Number(updatedData.unitCost) : current.rate);
      const minStock = updatedData.reorderLevel !== undefined ? Number(updatedData.reorderLevel) : (updatedData.minStock !== undefined ? Number(updatedData.minStock) : current.minStock);
      const currentStock = updatedData.currentStock !== undefined ? Number(updatedData.currentStock) : (updatedData.openingStock !== undefined ? Number(updatedData.openingStock) : current.currentStock);

      const merged = {
        ...current,
        ...updatedData,
        rate: rate,
        unitCost: rate,
        minStock: minStock,
        reorderLevel: minStock,
        currentStock: currentStock
      };
      this.data.items[idx] = merged;
      this.logActivity(`Updated item: ${merged.name}`, "Item Master", merged.code || id);
      this.saveState();

      const dbId = current.dbId || (typeof id === 'number' ? id : null);
      if (dbId) {
        try {
          await fetch(`/masters/items/${dbId}`, {
            method: 'PUT',
            headers: {
              'Content-Type': 'application/json',
              'Accept': 'application/json',
              'X-CSRF-TOKEN': this.getCsrfToken(),
              'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
              code: merged.code,
              name: merged.name,
              type: merged.type,
              category: merged.category,
              brand: merged.brand,
              fabric: merged.fabric,
              color: merged.color,
              size: merged.size,
              unit: merged.unit,
              unit_cost: merged.unitCost,
              current_stock: merged.currentStock,
              min_stock: merged.minStock,
              hsn_code: merged.hsn || merged.hsn_code,
              location: merged.location,
              status: merged.status
            })
          });
        } catch (e) {
          console.warn("Item update note:", e);
        }
      }
      return merged;
    }
  }

  async deleteItem(id) {
    const item = (this.data.items || []).find(i => i.id === id || i.code === id || (i.dbId && String(i.dbId) === String(id)));
    if (item) {
      const dbId = item.dbId || (typeof id === 'number' ? id : null);
      this.data.items = this.data.items.filter(i => i.id !== item.id && i.code !== item.code && (dbId ? i.dbId !== dbId : true));
      this.logActivity(`Deleted item: ${item.name}`, "Item Master", item.code || id);
      this.saveState();

      if (dbId) {
        try {
          await fetch(`/masters/items/${dbId}`, {
            method: 'DELETE',
            headers: {
              'Accept': 'application/json',
              'X-CSRF-TOKEN': this.getCsrfToken(),
              'X-Requested-With': 'XMLHttpRequest'
            }
          });
        } catch (e) {
          console.warn("Item delete note:", e);
        }
      }
    }
  }

  getItemById(id) {
    return (this.data.items || []).find(i => i.id === id || i.code === id || i.name.toLowerCase() === (id || '').toLowerCase());
  }

  // =========================================================================
  // 5. UNIT MASTER CRUD
  // =========================================================================
  addUnit(unit) {
    const nextNum = (this.data.units || []).length + 1;
    const name = (unit.name || "").trim();
    const code = (unit.code || name || `UNT-${String(nextNum).padStart(2, '0')}`).trim();
    const parentId = unit.parentId ? Number(unit.parentId) : null;
    let parentName = unit.parentName || null;
    if (parentId && !parentName) {
      const p = (this.data.units || []).find(u => u.id == parentId || u.dbId == parentId);
      if (p) parentName = p.name;
    }
    const conversionFactor = unit.conversionFactor !== null && unit.conversionFactor !== undefined && unit.conversionFactor !== "" ? Number(unit.conversionFactor) : null;

    const newUnit = {
      id: unit.id || nextNum,
      code: code,
      name: name,
      parentId: parentId,
      parentName: parentName,
      conversionFactor: conversionFactor,
      symbol: unit.symbol || code.toLowerCase(),
      decimalPlaces: Number(unit.decimalPlaces !== undefined ? unit.decimalPlaces : (unit.decimal_places !== undefined ? unit.decimal_places : 2)),
      description: unit.description || "",
      status: unit.status || "Active"
    };

    if (!this.data.units) this.data.units = [];
    this.data.units.push(newUnit);
    this.logActivity(`Added unit: ${newUnit.name}`, "Unit Master", newUnit.name);
    this.saveState();

    fetch('/masters/units', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': this.getCsrfToken(),
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify({
        code: newUnit.code,
        name: newUnit.name,
        parent_id: newUnit.parentId,
        conversion_factor: newUnit.conversionFactor,
        symbol: newUnit.symbol,
        decimal_places: newUnit.decimalPlaces,
        description: newUnit.description,
        status: newUnit.status
      })
    }).then(r => r.json()).then(res => {
      if (res && res.unit && res.unit.id) {
        newUnit.dbId = res.unit.id;
        newUnit.id = res.unit.id;
        if (res.unit.parent) newUnit.parentName = res.unit.parent.name;
        this.saveState();
      }
    }).catch(e => console.warn("Unit sync note:", e));

    return newUnit;
  }

  updateUnit(id, updatedData) {
    const idx = (this.data.units || []).findIndex(u => u.id == id || (u.dbId && u.dbId == id) || u.code === id);
    if (idx !== -1) {
      const current = this.data.units[idx];
      const name = (updatedData.name !== undefined ? updatedData.name : current.name).trim();
      const code = (updatedData.code !== undefined ? updatedData.code : current.code).trim();
      const parentId = updatedData.parentId !== undefined ? (updatedData.parentId ? Number(updatedData.parentId) : null) : current.parentId;
      let parentName = updatedData.parentName !== undefined ? updatedData.parentName : current.parentName;
      if (parentId && (!parentName || updatedData.parentId !== current.parentId)) {
        const p = (this.data.units || []).find(u => u.id == parentId || u.dbId == parentId);
        if (p) parentName = p.name;
      } else if (!parentId) {
        parentName = null;
      }
      const conversionFactor = updatedData.conversionFactor !== undefined ? (updatedData.conversionFactor !== null && updatedData.conversionFactor !== "" ? Number(updatedData.conversionFactor) : null) : current.conversionFactor;

      this.data.units[idx] = {
        ...current,
        ...updatedData,
        code,
        name,
        parentId,
        parentName,
        conversionFactor,
        status: updatedData.status || current.status
      };
      this.logActivity(`Updated unit: ${name}`, "Unit Master", name);
      this.saveState();

      const dbId = current.dbId || current.id;
      if (dbId) {
        fetch(`/masters/units/${dbId}`, {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': this.getCsrfToken(),
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: JSON.stringify({
            code: code,
            name: name,
            parent_id: parentId,
            conversion_factor: conversionFactor,
            symbol: updatedData.symbol !== undefined ? updatedData.symbol : current.symbol,
            decimal_places: updatedData.decimalPlaces !== undefined ? updatedData.decimalPlaces : current.decimalPlaces,
            description: updatedData.description !== undefined ? updatedData.description : current.description,
            status: updatedData.status || current.status
          })
        }).catch(e => console.warn("Unit update note:", e));
      }
    }
  }

  deleteUnit(id) {
    const unit = (this.data.units || []).find(u => u.id == id || u.code === id || (u.dbId && u.dbId == id));
    if (unit) {
      this.data.units = this.data.units.filter(u => u.id != id && u.code !== id && (!u.dbId || u.dbId != id));
      this.logActivity(`Deleted unit: ${unit.name} (${unit.code})`, "Unit Master", unit.code);
      this.saveState();

      const dbId = unit.dbId || unit.id;
      if (dbId) {
        fetch(`/masters/units/${dbId}`, {
          method: 'DELETE',
          headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': this.getCsrfToken(),
            'X-Requested-With': 'XMLHttpRequest'
          }
        }).catch(e => console.warn("Unit delete note:", e));
      }
    }
  }

  getUnitById(id) {
    if (id === null || id === undefined) return null;
    const strId = String(id).toLowerCase();
    return (this.data.units || []).find(u => 
      u.id == id || 
      (u.dbId && u.dbId == id) || 
      (u.code && u.code.toLowerCase() === strId) || 
      (u.name && u.name.toLowerCase() === strId)
    );
  }

  getUnitStats() {
    const units = this.data.units || [];
    return {
      total: units.length,
      active: units.filter(u => u.status === 'Active').length,
      inactive: units.filter(u => u.status === 'Inactive').length,
      decimals: units.filter(u => (u.decimalPlaces || 0) > 0).length
    };
  }

  // Backward compatibility safety
  addSize(sizeName) { if (!this.data.sizes) this.data.sizes = []; if (!this.data.sizes.includes(sizeName)) this.data.sizes.push(sizeName); }
  addColor(colorName, hex) { if (!this.data.colors) this.data.colors = []; this.data.colors.push({ name: colorName, hex: hex || '#000' }); }

  // =========================================================================
  // 6. JOB WORK & ASSIGNMENTS
  // =========================================================================
  addJobAssignment(job) {
    const nextNum = (this.data.jobWorks || []).length + 1;
    const id = `JW-2026-${String(nextNum).padStart(4, '0')}`;
    const newJob = {
      id,
      assignNo: String(nextNum).padStart(4, '0'),
      date: job.date || new Date().toISOString().split('T')[0],
      orderNo: job.orderNo || `SO-2026-${randNumber(1000, 9999)}`,
      customer: job.customer || "Internal Batch",
      item: job.item,
      process: job.process,
      jobWorker: job.jobWorker,
      jobWorkerId: job.jobWorkerId || "",
      freight: job.freight || "To Pay",
      factoryChallan: `FC-2026-${randNumber(8000, 9999)}`,
      lrNo: job.lrNo || `LR-${randNumber(10000, 99999)}-T`,
      transport: job.transport || "VRL Logistics",
      remark: job.remark || "",
      lotNo: job.lotNo || `LOT-2026-${randNumber(10000, 99999)}`,
      stage: `${job.process} Stage`,
      quantity: Number(job.quantity),
      rate: Number(job.rate || 20),
      totalAmount: Number(job.quantity) * Number(job.rate || 20),
      outwardDate: job.date || new Date().toISOString().split('T')[0],
      expectedReturnDate: job.expectedReturnDate || new Date().toISOString().split('T')[0],
      status: "In Progress",
      sentQty: Number(job.quantity),
      receivedGoodQty: 0,
      rejectedQty: 0,
      damagedQty: 0,
      pendingQty: Number(job.quantity),
      qcStatus: "Pending Return",
      items: job.items || [{ item: job.item, lotNo: job.lotNo, rate: job.rate, amount: Number(job.quantity) * Number(job.rate || 20) }]
    };

    if (!this.data.jobWorks) this.data.jobWorks = [];
    this.data.jobWorks.unshift(newJob);
    this.logActivity(`Issued Job Work: ${id} to ${job.jobWorker}`, "Job Work", id);
    this.saveState();

    fetch('/jobwork/assign', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': this.getCsrfToken(),
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify({
        job_worker_name: newJob.jobWorker,
        process_name: newJob.process,
        lot_number: newJob.lotNo,
        style_name: newJob.item,
        issue_date: newJob.date,
        due_date: newJob.expectedReturnDate,
        issued_qty: newJob.quantity,
        rate_per_piece: newJob.rate,
        instructions: newJob.remark
      })
    }).then(r => r.json()).then(res => {
      if (res && res.job_assignment && res.job_assignment.id) newJob.dbId = res.job_assignment.id;
    }).catch(e => console.warn("Job assign sync note:", e));

    return newJob;
  }

  deleteJobAssignment(id) {
    const j = (this.data.jobWorks || []).find(jw => jw.id === id);
    if (j) {
      this.data.jobWorks = this.data.jobWorks.filter(jw => jw.id !== id);
      this.logActivity(`Deleted Job Order: ${id}`, "Job Work", id);
      this.saveState();

      if (j.dbId) {
        fetch(`/jobwork/assign/${j.dbId}`, {
          method: 'DELETE',
          headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': this.getCsrfToken(),
            'X-Requested-With': 'XMLHttpRequest'
          }
        }).catch(e => console.warn("Job delete note:", e));
      }
    }
  }

  // =========================================================================
  // 8. PRODUCTION ORDERS & LOT TRACKING
  // =========================================================================
  addProductionOrder(order) {
    const nextNum = (this.data.productionOrders || []).length + 1;
    const id = `ORD-2026-${String(nextNum).padStart(3, '0')}`;
    const newOrder = {
      id,
      orderNo: id,
      customer: order.customer,
      product: order.product,
      itemCode: order.itemCode || order.product,
      quantity: Number(order.quantity),
      rate: Number(order.rate),
      amount: Number(order.quantity) * Number(order.rate),
      orderDate: order.orderDate || new Date().toISOString().split('T')[0],
      deliveryDate: order.deliveryDate || order.orderDate,
      stage: "Cutting",
      progress: 15,
      status: "In Production",
      priority: order.priority || "Normal"
    };

    if (!this.data.productionOrders) this.data.productionOrders = [];
    this.data.productionOrders.unshift(newOrder);
    this.data.salesOrders = this.data.productionOrders;
    this.logActivity(`Booked Production Order: ${id} for ${order.customer}`, "Production", id);
    this.saveState();

    fetch('/production/orders', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': this.getCsrfToken(),
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify({
        customer_name: newOrder.customer,
        style_name: newOrder.product,
        order_qty: newOrder.quantity,
        unit_price: newOrder.rate,
        order_date: newOrder.orderDate,
        delivery_date: newOrder.deliveryDate
      })
    }).then(r => r.json()).then(res => {
      if (res && res.order && res.order.id) newOrder.dbId = res.order.id;
    }).catch(e => console.warn("Prod order sync note:", e));

    return newOrder;
  }

  updateProductionOrder(id, updatedData) {
    const idx = (this.data.productionOrders || []).findIndex(p => p.id === id || p.orderNo === id);
    if (idx !== -1) {
      const current = this.data.productionOrders[idx];
      this.data.productionOrders[idx] = { ...current, ...updatedData };
      this.saveState();

      if (current.dbId) {
        fetch(`/production/orders/${current.dbId}`, {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': this.getCsrfToken(),
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: JSON.stringify(updatedData)
        }).catch(e => console.warn("Prod order update note:", e));
      }
    }
  }

  deleteProductionOrder(id) {
    const p = (this.data.productionOrders || []).find(po => po.id === id || po.orderNo === id);
    if (p) {
      this.data.productionOrders = this.data.productionOrders.filter(po => po.id !== id && po.orderNo !== id);
      this.data.salesOrders = this.data.productionOrders;
      this.logActivity(`Deleted Production Order: ${id}`, "Production", id);
      this.saveState();

      if (p.dbId) {
        fetch(`/production/orders/${p.dbId}`, {
          method: 'DELETE',
          headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': this.getCsrfToken(),
            'X-Requested-With': 'XMLHttpRequest'
          }
        }).catch(e => console.warn("Prod order delete note:", e));
      }
    }
  }

  addQualityCheck(qc) {
    const nextNum = (this.data.qualityChecks || []).length + 1;
    const id = `QC-2026-${String(nextNum).padStart(3, '0')}`;
    const totalDefects = (Number(qc.minorDefects || 0)) + (Number(qc.majorDefects || 0));
    const defectPct = ((totalDefects / Number(qc.inspectedQty)) * 100).toFixed(2);

    const newQc = {
      id,
      orderNo: qc.orderNo,
      lotNumber: qc.lotNumber,
      styleName: qc.styleName,
      date: qc.date || new Date().toISOString().split('T')[0],
      inspector: qc.inspector || "QC Inspector",
      inspectedQty: Number(qc.inspectedQty),
      passedQty: Number(qc.passedQty),
      minorDefects: Number(qc.minorDefects || 0),
      majorDefects: Number(qc.majorDefects || 0),
      defectPercent: Number(defectPct),
      status: Number(defectPct) > 5 ? "Rework Required" : "Passed",
      remarks: qc.remarks || ""
    };

    if (!this.data.qualityChecks) this.data.qualityChecks = [];
    this.data.qualityChecks.unshift(newQc);
    this.logActivity(`Logged QC Inspection: ${id} (${newQc.status})`, "Quality Check", id);
    this.saveState();

    fetch('/production/qc', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': this.getCsrfToken(),
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify({
        order_no: newQc.orderNo,
        lot_number: newQc.lotNumber,
        style_name: newQc.styleName,
        inspection_date: newQc.date,
        inspector_name: newQc.inspector,
        total_inspected: newQc.inspectedQty,
        passed_qty: newQc.passedQty,
        minor_defects: newQc.minorDefects,
        major_defects: newQc.majorDefects,
        remarks: newQc.remarks
      })
    }).then(r => r.json()).then(res => {
      if (res && res.qc && res.qc.id) newQc.dbId = res.qc.id;
    }).catch(e => console.warn("QC sync note:", e));

    return newQc;
  }

  // =========================================================================
  // 9. QR VOUCHERS
  // =========================================================================
  createQrVoucher(voucher) {
    const randomSuffix = Math.random().toString(36).substring(2, 7).toUpperCase();
    const code = `FASHION-${voucher.discountPercent}-${randomSuffix}`;
    const newVoucher = {
      id: code,
      code,
      customerName: voucher.customerName || "General Promotion",
      discountPercent: Number(voucher.discountPercent),
      maxDiscountCap: Number(voucher.maxDiscountCap || 5000),
      minOrderValue: Number(voucher.minOrderValue || 25000),
      validFrom: new Date().toISOString().split('T')[0],
      validUntil: voucher.validUntil || new Date(Date.now() + 90 * 86400000).toISOString().split('T')[0],
      isRedeemed: false,
      status: "Active"
    };

    if (!this.data.qrVouchers) this.data.qrVouchers = [];
    this.data.qrVouchers.unshift(newVoucher);
    this.data.discountCoupons = this.data.qrVouchers;
    this.logActivity(`Created Discount QR Voucher: ${code} (${voucher.discountPercent}%)`, "QR Management", code);
    this.saveState();

    fetch('/qr/generator', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': this.getCsrfToken(),
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify({
        customer_name: newVoucher.customerName,
        discount_percent: newVoucher.discountPercent,
        max_discount_cap: newVoucher.maxDiscountCap,
        min_order_value: newVoucher.minOrderValue,
        valid_until: newVoucher.validUntil
      })
    }).then(r => r.json()).then(res => {
      if (res && res.voucher && res.voucher.id) newVoucher.dbId = res.voucher.id;
    }).catch(e => console.warn("QR sync note:", e));

    return newVoucher;
  }

  // =========================================================================
  // 10. DISPATCH & CHALLANS
  // =========================================================================
  addDispatchChallan(dc) {
    const nextNum = (this.data.dispatchChallans || []).length + 1;
    const id = `DC-2026-${String(nextNum).padStart(3, '0')}`;
    const newDc = {
      id,
      challanNo: id,
      orderNo: dc.orderNo,
      customer: dc.customer,
      dispatchDate: dc.dispatchDate || new Date().toISOString().split('T')[0],
      transporter: dc.transporter,
      lrNumber: dc.lrNumber || `LR-${randNumber(10000, 99999)}`,
      vehicleNumber: dc.vehicleNumber || `MH-04-${randNumber(1000, 9999)}`,
      destination: dc.destination || "Mumbai",
      totalCartons: Number(dc.totalCartons || 10),
      totalQty: Number(dc.totalQty || 100),
      status: "In Transit"
    };

    if (!this.data.dispatchChallans) this.data.dispatchChallans = [];
    this.data.dispatchChallans.unshift(newDc);
    this.logActivity(`Dispatched Order: ${dc.orderNo} via Challan ${id}`, "Dispatch", id);
    this.saveState();

    fetch('/dispatch/challans', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': this.getCsrfToken(),
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify({
        order_no: newDc.orderNo,
        customer_name: newDc.customer,
        dispatch_date: newDc.dispatchDate,
        transporter_name: newDc.transporter,
        lr_number: newDc.lrNumber,
        vehicle_number: newDc.vehicleNumber,
        destination_city: newDc.destination,
        total_cartons: newDc.totalCartons,
        total_qty: newDc.totalQty
      })
    }).then(r => r.json()).then(res => {
      if (res && res.challan && res.challan.id) newDc.dbId = res.challan.id;
    }).catch(e => console.warn("Dispatch sync note:", e));

    return newDc;
  }

  deleteDispatchChallan(id) {
    const dc = (this.data.dispatchChallans || []).find(d => d.id === id || d.challanNo === id);
    if (dc) {
      this.data.dispatchChallans = this.data.dispatchChallans.filter(d => d.id !== id && d.challanNo !== id);
      this.logActivity(`Deleted Dispatch Challan: ${id}`, "Dispatch", id);
      this.saveState();

      if (dc.dbId) {
        fetch(`/dispatch/challans/${dc.dbId}`, {
          method: 'DELETE',
          headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': this.getCsrfToken(),
            'X-Requested-With': 'XMLHttpRequest'
          }
        }).catch(e => console.warn("Dispatch delete note:", e));
      }
    }
  }

  // =========================================================================
  // 11. INVOICES & BILLING
  // =========================================================================
  addInvoice(inv) {
    const nextNum = (this.data.invoices || []).length + 1;
    const id = `INV-2026-${String(nextNum).padStart(3, '0')}`;
    const subtotal = inv.items.reduce((s, it) => s + (Number(it.qty) * Number(it.rate)), 0);
    const discountAmount = Number(inv.discountAmount || 0);
    const taxableAmount = Math.max(0, subtotal - discountAmount);
    const cgstAmount = (taxableAmount * 0.025);
    const sgstAmount = (taxableAmount * 0.025);
    const grandTotal = taxableAmount + cgstAmount + sgstAmount;

    const newInv = {
      id,
      invoiceNo: id,
      orderNo: inv.orderNo || "",
      customer: inv.customer,
      companyName: inv.companyName || inv.customer,
      invoiceDate: inv.invoiceDate || new Date().toISOString().split('T')[0],
      dueDate: inv.dueDate || new Date().toISOString().split('T')[0],
      subtotal,
      discountAmount,
      voucherCode: inv.voucherCode || "",
      taxableAmount,
      cgstAmount,
      sgstAmount,
      amount: grandTotal,
      grandTotal,
      paidAmount: 0,
      balanceAmount: grandTotal,
      status: "Sent",
      items: inv.items
    };

    if (!this.data.invoices) this.data.invoices = [];
    this.data.invoices.unshift(newInv);

    // Update customer outstanding
    const cust = this.getCustomerById(inv.customer);
    if (cust) {
      cust.outstanding = (Number(cust.outstanding) || 0) + grandTotal;
      this.updateCustomer(cust.id, { outstanding: cust.outstanding });
    }

    this.logActivity(`Issued Invoice: ${id} to ${inv.customer} for ₹${grandTotal.toLocaleString('en-IN')}`, "Invoices", id);
    this.saveState();

    fetch('/invoices/store', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': this.getCsrfToken(),
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify({
        customer_name: newInv.customer,
        company_name: newInv.companyName,
        order_no: newInv.orderNo,
        invoice_date: newInv.invoiceDate,
        due_date: newInv.dueDate,
        voucher_code: newInv.voucherCode,
        items: newInv.items.map(it => ({
          item_description: it.description || it.item,
          qty: it.qty,
          rate: it.rate
        }))
      })
    }).then(r => r.json()).then(res => {
      if (res && res.invoice && res.invoice.id) newInv.dbId = res.invoice.id;
    }).catch(e => console.warn("Invoice sync note:", e));

    return newInv;
  }

  deleteInvoice(id) {
    const inv = (this.data.invoices || []).find(i => i.id === id || i.invoiceNo === id);
    if (inv) {
      this.data.invoices = this.data.invoices.filter(i => i.id !== id && i.invoiceNo !== id);
      this.logActivity(`Deleted Invoice: ${id}`, "Invoices", id);
      this.saveState();

      if (inv.dbId) {
        fetch(`/invoices/list/${inv.dbId}`, {
          method: 'DELETE',
          headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': this.getCsrfToken(),
            'X-Requested-With': 'XMLHttpRequest'
          }
        }).catch(e => console.warn("Invoice delete note:", e));
      }
    }
  }

  // =========================================================================
  // 12. ACCOUNTS & SETTLEMENTS (RECEIPTS)
  // =========================================================================
  recordPayment(paymentData) {
    if (!this.data.payments) this.data.payments = [];
    const nextNum = this.data.payments.length + 1;
    const id = `REC-${new Date().getFullYear()}-${String(nextNum).padStart(4, '0')}`;

    const newPayment = {
      id,
      customer: paymentData.customer,
      invoiceNo: paymentData.invoiceNo || "",
      date: paymentData.date || new Date().toISOString().split('T')[0],
      amount: Number(paymentData.amount || 0),
      mode: paymentData.mode || "Bank Transfer (NEFT)",
      refNo: paymentData.refNo || `UTR${Date.now().toString().slice(-8)}`,
      remarks: paymentData.remarks || "Payment received and accounted"
    };

    this.data.payments.unshift(newPayment);

    // Deduct Customer Outstanding Dynamically
    const cust = this.getCustomerById(paymentData.customer);
    if (cust) {
      cust.outstanding = Math.max(0, (Number(cust.outstanding) || 0) - newPayment.amount);
      this.updateCustomer(cust.id, { outstanding: cust.outstanding });
    }

    // Deduct Invoice Balance if linked
    if (paymentData.invoiceNo && this.data.invoices) {
      const inv = this.data.invoices.find(i => i.invoiceNo === paymentData.invoiceNo || i.id === paymentData.invoiceNo);
      if (inv) {
        inv.paidAmount = (Number(inv.paidAmount) || 0) + newPayment.amount;
        inv.balanceAmount = Math.max(0, (Number(inv.amount || inv.grandTotal) || 0) - inv.paidAmount);
        inv.status = inv.balanceAmount <= 0 ? "Paid" : "Partially Paid";
      }
    }

    this.logActivity(`Received payment ₹${newPayment.amount.toLocaleString('en-IN')} from ${newPayment.customer}`, "Accounts", id);
    this.saveState();

    fetch('/accounts/receipt', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': this.getCsrfToken(),
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify({
        customer_name: newPayment.customer,
        payment_date: newPayment.date,
        amount: newPayment.amount,
        payment_mode: newPayment.mode,
        reference_no: newPayment.refNo,
        notes: newPayment.remarks
      })
    }).then(r => r.json()).then(res => {
      if (res && res.payment && res.payment.id) newPayment.dbId = res.payment.id;
    }).catch(e => console.warn("Receipt sync note:", e));

    return newPayment;
  }

  // =========================================================================
  // 13. ADMIN USER & COMPANY SETTINGS
  // =========================================================================
  addUser(user) {
    const nextNum = (this.data.users || []).length + 1;
    const id = `USR-${String(nextNum).padStart(3, '0')}`;
    const newUser = {
      id,
      name: user.name,
      email: user.email,
      role: user.role || "Store Manager",
      status: "Active",
      lastLogin: "Just now"
    };

    if (!this.data.users) this.data.users = [];
    this.data.users.unshift(newUser);
    this.logActivity(`Created system user: ${newUser.name} (${newUser.role})`, "Admin", id);
    this.saveState();

    fetch('/admin/users', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': this.getCsrfToken(),
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify({
        name: newUser.name,
        email: newUser.email,
        role: newUser.role,
        password: user.password || "admin123"
      })
    }).then(r => r.json()).then(res => {
      if (res && res.user && res.user.id) newUser.dbId = res.user.id;
    }).catch(e => console.warn("User create note:", e));

    return newUser;
  }

  updateCompanySettings(settings) {
    this.data.company = { ...this.data.company, ...settings };
    this.logActivity("Updated company profile & ERP configuration", "Admin", "CompanySettings");
    this.saveState();

    fetch('/admin/settings', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': this.getCsrfToken(),
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify(settings)
    }).catch(e => console.warn("Company settings update note:", e));
  }
}

// Utility Helper
function randNumber(min, max) {
  return Math.floor(Math.random() * (max - min + 1)) + min;
}

// Singleton ERP State Instance
const ERPState = new ERPStateManager();

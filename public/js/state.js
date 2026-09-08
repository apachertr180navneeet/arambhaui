/* ==========================================================================
   REACTIVE STATE MANAGER & CONNECTED ERP BUSINESS LOGIC
   GarmentERP - FashionWorks Pvt. Ltd.
   ========================================================================== */

class ERPStateManager {
  constructor() {
    this.STORAGE_KEY = 'garment_erp_data_v1';
    this.listeners = [];
    this.data = this.loadState();
  }

  // Load or initialize state
  loadState() {
    try {
      const saved = localStorage.getItem(this.STORAGE_KEY);
      if (saved) {
        return JSON.parse(saved);
      }
    } catch (e) {
      console.warn("Could not load from localStorage:", e);
    }
    return JSON.parse(JSON.stringify(INITIAL_DATA));
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
  }

  subscribe(listener) {
    this.listeners.push(listener);
    return () => {
      this.listeners = this.listeners.filter(l => l !== listener);
    };
  }

  notify() {
    this.listeners.forEach(fn => fn(this.data));
  }

  // Audit Logging Helper
  logActivity(action, module, record, user = this.data.currentUser.name) {
    const newLog = {
      id: `LOG-${String(this.data.activityLogs.length + 1).padStart(3, '0')}`,
      user: user,
      role: this.data.currentUser.role,
      action: action,
      module: module,
      record: record,
      time: "Just now",
      ip: "192.168.1." + Math.floor(Math.random() * 80 + 10)
    };
    this.data.activityLogs.unshift(newLog);
    if (this.data.activityLogs.length > 50) {
      this.data.activityLogs.pop();
    }
  }

  // CSRF Token Helper
  getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
  }

  // --- 1. CUSTOMERS ---
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
    this.logActivity(`Added new customer: ${newCust.name} (${id})`, "Customer Master", id);
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
      }).catch(err => console.warn("Background API sync note:", err));
    } catch (e) {
      console.warn("Could not dispatch async customer create:", e);
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
        phone: updatedData.phone || updatedData.mobile || current.phone || current.mobile,
        gstin: (updatedData.gstin !== undefined ? updatedData.gstin : current.gstin || "").toUpperCase(),
        creditLimit: updatedData.creditLimit !== undefined ? Number(updatedData.creditLimit) : current.creditLimit,
        outstanding: updatedData.outstanding !== undefined ? Number(updatedData.outstanding) : current.outstanding
      };
      this.data.customers[idx] = merged;
      this.logActivity(`Updated customer details: ${merged.name}`, "Customer Master", id);
      this.saveState();

      // Async sync with Laravel Backend
      const targetId = current.dbId || id;
      try {
        fetch(`/masters/customers/${targetId}`, {
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
        }).catch(err => console.warn("Background API update note:", err));
      } catch (e) {
        console.warn("Could not dispatch async customer update:", e);
      }

      return merged;
    }
    return null;
  }

  deleteCustomer(id) {
    const cust = (this.data.customers || []).find(c => c.id === id || c.code === id || c.name === id);
    if (cust) {
      this.data.customers = this.data.customers.filter(c => c.id !== id && c.code !== id);
      this.logActivity(`Deleted customer: ${cust.name}`, "Customer Master", id);
      this.saveState();

      // Async sync with Laravel Backend
      const targetId = cust.dbId || id;
      try {
        fetch(`/masters/customers/${targetId}`, {
          method: 'DELETE',
          headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': this.getCsrfToken(),
            'X-Requested-With': 'XMLHttpRequest'
          }
        }).catch(err => console.warn("Background API delete note:", err));
      } catch (e) {
        console.warn("Could not dispatch async customer delete:", e);
      }
    }
  }

  getCustomerById(idOrName) {
    if (!idOrName) return null;
    return (this.data.customers || []).find(c => 
      c.id === idOrName || 
      c.code === idOrName || 
      (c.name && c.name.toLowerCase() === idOrName.toLowerCase())
    );
  }

  getCustomerStats() {
    const list = this.data.customers || [];
    const total = list.length;
    const active = list.filter(c => c.status === "Active").length;
    const inactive = list.filter(c => c.status === "Inactive").length;
    const blocked = list.filter(c => c.status === "Blocked").length;
    const totalOutstanding = list.reduce((sum, c) => sum + (Number(c.outstanding) || 0), 0);
    const totalCreditLimit = list.reduce((sum, c) => sum + (Number(c.creditLimit) || 0), 0);
    const creditUtilization = totalCreditLimit > 0 ? ((totalOutstanding / totalCreditLimit) * 100).toFixed(1) : 0;
    const highRisk = list.filter(c => (Number(c.outstanding) || 0) >= (Number(c.creditLimit) || 0) * 0.9 && (Number(c.outstanding) || 0) > 0).length;
    const cities = [...new Set(list.map(c => c.city).filter(Boolean))].sort();

    return {
      total,
      active,
      inactive,
      blocked,
      totalOutstanding,
      totalCreditLimit,
      creditUtilization,
      highRisk,
      cities
    };
  }

  getCustomerStatement(customerIdOrName) {
    const cust = this.getCustomerById(customerIdOrName);
    if (!cust) return null;

    const invoices = (this.data.invoices || []).filter(i => 
      i.customerId === cust.id || 
      (i.customer && i.customer.toLowerCase() === cust.name.toLowerCase())
    );

    const payments = (this.data.payments || []).filter(p => 
      p.customerId === cust.id || 
      (p.customer && p.customer.toLowerCase() === cust.name.toLowerCase())
    );

    const orders = (this.data.salesOrders || []).filter(o => 
      o.customerId === cust.id || 
      (o.customer && o.customer.toLowerCase() === cust.name.toLowerCase())
    );

    // Build chronological ledger entries
    const entries = [];

    // Add invoices (Debit entries)
    invoices.forEach(inv => {
      entries.push({
        date: inv.date || "2026-08-01",
        type: "Sales Invoice",
        ref: inv.invoiceNo || "INV-NEW",
        orderNo: inv.orderNo || "-",
        description: `Tax Invoice - ${inv.items ? inv.items.length + ' item(s)' : 'Goods supplied'}`,
        debit: Number(inv.amount || 0),
        credit: 0,
        status: inv.status || "Unpaid"
      });
    });

    // Add payments (Credit entries)
    payments.forEach(pay => {
      entries.push({
        date: pay.date || new Date().toISOString().split('T')[0],
        type: "Payment Receipt",
        ref: pay.id || "REC-NEW",
        orderNo: pay.invoiceNo || "-",
        description: `Payment Received via ${pay.mode || 'Bank'} (Ref: ${pay.refNo || 'Direct'})`,
        debit: 0,
        credit: Number(pay.amount || 0),
        status: "Received"
      });
    });

    // Sort by date ascending
    entries.sort((a, b) => new Date(a.date) - new Date(b.date));

    // Calculate running balance
    let runningBalance = 0;
    const ledgerWithBalance = entries.map(entry => {
      runningBalance += (entry.debit - entry.credit);
      return {
        ...entry,
        balance: runningBalance
      };
    });

    const totalInvoiced = invoices.reduce((sum, i) => sum + (Number(i.amount) || 0), 0);
    const totalReceived = payments.reduce((sum, p) => sum + (Number(p.amount) || 0), 0);
    const calculatedOutstanding = totalInvoiced > 0 ? Math.max(0, totalInvoiced - totalReceived) : (cust.outstanding || 0);

    return {
      customer: cust,
      invoices,
      payments,
      orders,
      ledger: ledgerWithBalance,
      totalInvoiced,
      totalReceived,
      calculatedOutstanding,
      creditLimit: cust.creditLimit,
      creditAvailable: Math.max(0, (cust.creditLimit || 0) - (cust.outstanding || 0))
    };
  }

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

    // Update Customer Outstanding Dynamically
    const cust = this.getCustomerById(paymentData.customer);
    if (cust) {
      cust.outstanding = Math.max(0, (Number(cust.outstanding) || 0) - newPayment.amount);
      this.updateCustomer(cust.id, { outstanding: cust.outstanding });
    }

    // If linked invoice exists, update its status
    if (paymentData.invoiceNo && this.data.invoices) {
      const inv = this.data.invoices.find(i => i.invoiceNo === paymentData.invoiceNo);
      if (inv) {
        inv.paidAmount = (Number(inv.paidAmount) || 0) + newPayment.amount;
        inv.balanceAmount = Math.max(0, (Number(inv.amount) || 0) - inv.paidAmount);
        inv.status = inv.balanceAmount <= 0 ? "Paid" : "Partially Paid";
      }
    }

    this.logActivity(`Received payment of ₹${newPayment.amount.toLocaleString('en-IN')} from ${newPayment.customer} (Ref: ${newPayment.refNo})`, "Settlements", id);
    this.saveState();
    return newPayment;
  }

  // --- 2. VENDORS ---
  addVendor(vendor) {
    const id = `VND-${String(this.data.vendors.length + 1).padStart(3, '0')}`;
    const newVendor = {
      id,
      ...vendor,
      outstanding: Number(vendor.openingBalance || 0),
      status: vendor.status || "Active"
    };
    this.data.vendors.unshift(newVendor);
    this.logActivity(`Added vendor: ${newVendor.name}`, "Vendor Master", id);
    this.saveState();
    return newVendor;
  }

  updateVendor(id, updatedData) {
    const idx = this.data.vendors.findIndex(v => v.id === id);
    if (idx !== -1) {
      this.data.vendors[idx] = { ...this.data.vendors[idx], ...updatedData };
      this.logActivity(`Updated vendor: ${this.data.vendors[idx].name}`, "Vendor Master", id);
      this.saveState();
    }
  }

  deleteVendor(id) {
    const vnd = this.data.vendors.find(v => v.id === id);
    if (vnd) {
      this.data.vendors = this.data.vendors.filter(v => v.id !== id);
      this.logActivity(`Deleted vendor: ${vnd.name}`, "Vendor Master", id);
      this.saveState();
    }
  }

  // --- 3. JOB WORKERS ---
  addJobWorker(worker) {
    const id = `JWK-${String(this.data.jobWorkers.length + 1).padStart(3, '0')}`;
    const newWorker = {
      id,
      ...worker,
      outstanding: 0,
      status: worker.status || "Active"
    };
    this.data.jobWorkers.unshift(newWorker);
    this.logActivity(`Added job worker: ${newWorker.name} (${newWorker.process})`, "Job Worker Master", id);
    this.saveState();
    return newWorker;
  }

  updateJobWorker(id, updatedData) {
    const idx = this.data.jobWorkers.findIndex(w => w.id === id);
    if (idx !== -1) {
      this.data.jobWorkers[idx] = { ...this.data.jobWorkers[idx], ...updatedData };
      this.logActivity(`Updated job worker: ${this.data.jobWorkers[idx].name}`, "Job Worker Master", id);
      this.saveState();
    }
  }

  deleteJobWorker(id) {
    const jw = this.data.jobWorkers.find(w => w.id === id);
    if (jw) {
      this.data.jobWorkers = this.data.jobWorkers.filter(w => w.id !== id);
      this.logActivity(`Deleted job worker: ${jw.name}`, "Job Worker Master", id);
      this.saveState();
    }
  }

  // --- 4. ITEM MASTER ---
  addItem(item) {
    const id = `ITM-${String(this.data.items.length + 1).padStart(3, '0')}`;
    const newItem = {
      id,
      ...item,
      openingStock: Number(item.openingStock || 0),
      inwardStock: 0,
      outwardStock: 0,
      currentStock: Number(item.openingStock || 0),
      reorderLevel: Number(item.reorderLevel || 100),
      rate: Number(item.rate || 0),
      status: item.status || "Active"
    };
    this.data.items.unshift(newItem);
    
    // Add ledger entry for opening stock
    if (newItem.openingStock > 0) {
      this.data.itemLedger.unshift({
        date: new Date().toISOString().split('T')[0],
        ref: `OB-${id}`,
        item: newItem.name,
        type: "Opening Stock",
        inward: newItem.openingStock,
        outward: 0,
        balance: newItem.openingStock,
        user: this.data.currentUser.name,
        lot: "INIT-LOT"
      });
    }

    this.logActivity(`Added item: ${newItem.name} (${newItem.code})`, "Item Master", id);
    this.saveState();
    return newItem;
  }

  updateItem(id, updatedData) {
    const idx = this.data.items.findIndex(i => i.id === id);
    if (idx !== -1) {
      this.data.items[idx] = { ...this.data.items[idx], ...updatedData };
      this.logActivity(`Updated item: ${this.data.items[idx].name}`, "Item Master", id);
      this.saveState();
    }
  }

  deleteItem(id) {
    const itm = this.data.items.find(i => i.id === id);
    if (itm) {
      this.data.items = this.data.items.filter(i => i.id !== id);
      this.logActivity(`Deleted item: ${itm.name}`, "Item Master", id);
      this.saveState();
    }
  }

  // --- 5. CUSTOMER SALES ORDERS (SO) ---
  createSalesOrder(orderData) {
    const count = this.data.salesOrders.length + 1045;
    const id = `SO-2026-${count}`;
    const pchNo = orderData.pchNo || String(this.data.salesOrders.length + 8).padStart(4, '0');
    const bno = orderData.bno || `BNO-2026-${String(this.data.salesOrders.length + 101).padStart(3, '0')}`;
    
    // Process items array
    const items = orderData.items && orderData.items.length > 0 ? orderData.items : [{
      lotNo: `dh/0000/${pchNo}/01`,
      item: orderData.product || "100% Combed Cotton Fabric 180 GSM",
      stage: orderData.stage || "Grey",
      qty: Number(orderData.quantity || 5000),
      rate: Number(orderData.rate || 145),
      amount: Number(orderData.amount || (orderData.quantity || 5000) * (orderData.rate || 145)),
      transport: orderData.transport || "VRL Logistics",
      lrNo: orderData.lrNo || "",
      netMeter: Number(orderData.netMeter || orderData.quantity || 5000)
    }];

    const totalQty = items.reduce((acc, it) => acc + Number(it.qty || it.netMeter || 0), 0);
    const totalAmount = items.reduce((acc, it) => acc + Number(it.amount || ((it.qty || 0) * (it.rate || 0))), 0);
    const primaryProduct = items[0] ? items[0].item : (orderData.product || "Fabric");
    const primaryLotNo = items.map(i => i.lotNo).filter(Boolean).join(', ') || `dh/0000/${pchNo}/01`;
    const primaryStage = items[0] ? items[0].stage : "Grey";

    const newSO = {
      id,
      pchNo,
      bno,
      customer: orderData.customer,
      customerId: orderData.customerId || "CUST-001",
      orderDate: orderData.date || orderData.orderDate || new Date().toISOString().split('T')[0],
      deliveryDate: orderData.deliveryDate || new Date(Date.now() + 15 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
      product: primaryProduct,
      itemCode: orderData.itemCode || "FAB-COT-180",
      freight: orderData.freight || "To Pay",
      remark: orderData.remark || "",
      quantity: totalQty,
      rate: items[0] ? items[0].rate : Number(orderData.rate || 145),
      amount: totalAmount,
      priority: orderData.priority || "Normal",
      status: "In Production",
      lotNo: primaryLotNo,
      stage: primaryStage,
      items: items
    };

    this.data.salesOrders.unshift(newSO);

    // Initialize Lot Timeline Tracking for each item in the order
    items.forEach(it => {
      const lotCode = it.lotNo || `dh/0000/${pchNo}/01`;
      this.data.lots.unshift({
        lotNo: lotCode,
        orderNo: id,
        customer: orderData.customer,
        product: it.item,
        targetQty: Number(it.qty || it.netMeter || totalQty),
        currentQty: Number(it.qty || it.netMeter || totalQty),
        currentProcess: it.stage || "Grey Fabric Inward",
        status: "In Progress",
        qrCodeString: `GARMENT-LOT-${lotCode}`,
        timeline: [
          {
            date: new Date().toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }),
            time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
            stage: "Order Booked",
            qty: Number(it.qty || totalQty),
            operator: this.data.currentUser.name,
            note: `Sales Order ${id} (PCH No: ${pchNo}, BNO: ${bno}) booked for ${orderData.customer} - ${it.item}`
          }
        ]
      });
    });

    this.logActivity(`Created Sales Order: ${id} (PCH #${pchNo}) - ${items.length} items (${totalQty} mtr for ${orderData.customer})`, "Customer Orders", id);
    this.saveState();
    return newSO;
  }

  // --- 6. PURCHASE ORDERS & INWARDS (GRN) ---
  createPurchaseOrder(poData) {
    const id = `PO-2026-${String(this.data.purchaseOrders.length + 816).padStart(4, '0')}`;
    const subtotal = poData.items.reduce((acc, item) => acc + (Number(item.qty) * Number(item.rate)), 0);
    const taxTotal = poData.items.reduce((acc, item) => acc + ((Number(item.qty) * Number(item.rate) * (Number(item.tax) || 5)) / 100), 0);
    
    const newPO = {
      id,
      vendor: poData.vendor,
      vendorId: poData.vendorId,
      poDate: poData.poDate || new Date().toISOString().split('T')[0],
      expectedDate: poData.expectedDate,
      warehouse: poData.warehouse || "Main Raw Material Store - Unit 1",
      items: poData.items.map(it => ({
        item: it.item,
        code: it.code || "FAB-COT-180",
        qty: Number(it.qty),
        unit: it.unit || "Meters",
        rate: Number(it.rate),
        tax: Number(it.tax || 5),
        amount: Number(it.qty) * Number(it.rate) * (1 + (Number(it.tax || 5) / 100))
      })),
      subtotal,
      taxTotal,
      grandTotal: subtotal + taxTotal,
      status: "Approved",
      paymentStatus: "Pending"
    };

    this.data.purchaseOrders.unshift(newPO);
    this.logActivity(`Created Purchase Order: ${id} to ${poData.vendor} (₹${(subtotal + taxTotal).toLocaleString('en-IN')})`, "Purchase Orders", id);
    this.saveState();
    return newPO;
  }

  createPurchaseInward(inwardData) {
    const id = `GRN-2026-${String(this.data.purchaseInwards.length + 411).padStart(4, '0')}`;
    const lotNumber = `RAW-LOT-${Date.now().toString().slice(-6)}`;
    const acceptedQty = Number(inwardData.receivedQty) - Number(inwardData.rejectedQty || 0);

    const newGRN = {
      id,
      poNumber: inwardData.poNumber,
      vendor: inwardData.vendor,
      receivedDate: inwardData.receivedDate || new Date().toISOString().split('T')[0],
      warehouse: inwardData.warehouse || "Main Raw Material Store - Unit 1",
      item: inwardData.item,
      orderedQty: Number(inwardData.orderedQty),
      receivedQty: Number(inwardData.receivedQty),
      rejectedQty: Number(inwardData.rejectedQty || 0),
      acceptedQty,
      lotNumber,
      inspectedBy: this.data.currentUser.name,
      status: "Approved"
    };

    this.data.purchaseInwards.unshift(newGRN);

    // Update Item Stock Automatically
    const itm = this.data.items.find(i => i.name.toLowerCase() === inwardData.item.toLowerCase() || i.code === inwardData.itemCode);
    if (itm) {
      itm.inwardStock += acceptedQty;
      itm.currentStock += acceptedQty;

      // Add to Item Ledger
      this.data.itemLedger.unshift({
        date: newGRN.receivedDate,
        ref: id,
        item: itm.name,
        type: "Purchase Inward (GRN)",
        inward: acceptedQty,
        outward: 0,
        balance: itm.currentStock,
        user: this.data.currentUser.name,
        lot: lotNumber
      });
    }

    // Update PO Status if linked
    const po = this.data.purchaseOrders.find(p => p.id === inwardData.poNumber);
    if (po) {
      po.status = "Received";
    }

    this.logActivity(`Received GRN: ${id} (${acceptedQty} units of ${inwardData.item})`, "Purchase Inward", id);
    this.saveState();
    return newGRN;
  }

  // --- 7. JOB WORK & OUTWARD / INWARD ---
  assignJobWork(jwData) {
    const id = jwData.assignNo ? `JW-2026-${String(jwData.assignNo).padStart(4, '0')}` : `JW-2026-${String(this.data.jobWorks.length + 26).padStart(4, '0')}`;
    
    // Process items array
    const items = jwData.items && jwData.items.length > 0 ? jwData.items : [{
      item: jwData.item || "Fabric / Garment Quality",
      lotNo: jwData.lotNo || "LOT-2026-00145",
      stage: jwData.stage || "Cutting",
      meter: Number(jwData.meter || jwData.quantity || 500),
      netMeter: Number(jwData.netMeter || jwData.quantity || 500),
      process: jwData.process || "Stitching",
      lrNo: jwData.lrNo || "",
      transport: jwData.transport || "",
      rate: Number(jwData.rate || 20),
      amount: Number(jwData.totalAmount || (jwData.quantity || 500) * (jwData.rate || 20))
    }];

    const totalMeters = items.reduce((sum, it) => sum + Number(it.meter || 0), 0);
    const totalNetMeters = items.reduce((sum, it) => sum + Number(it.netMeter || 0), 0);
    const totalQty = totalNetMeters || Number(jwData.quantity || totalMeters || 1000);
    const rate = Number(jwData.rate || (items[0] && items[0].rate) || 20);
    const totalAmount = Number(jwData.totalAmount || (totalQty * rate));

    const primaryItem = items[0] ? items[0].item : (jwData.item || "Fabric");
    const primaryLotNo = items.map(i => i.lotNo).filter(Boolean).join(", ") || (jwData.lotNo || "LOT-2026-00145");
    const primaryProcess = items.map(i => i.process).filter(Boolean).join(", ") || (jwData.process || "Stitching");
    const primaryStage = items[0] ? items[0].stage : (jwData.stage || "Stage 1");
    const primaryLRNo = items.map(i => i.lrNo).filter(Boolean).join(", ") || (jwData.lrNo || "");
    const primaryTransport = items.map(i => i.transport).filter(Boolean).join(", ") || (jwData.transport || "");

    const newJW = {
      id,
      assignNo: jwData.assignNo || String(this.data.jobWorks.length + 1).padStart(4, '0'),
      date: jwData.date || jwData.outwardDate || new Date().toISOString().split('T')[0],
      outwardDate: jwData.date || jwData.outwardDate || new Date().toISOString().split('T')[0],
      expectedReturnDate: jwData.expectedReturnDate || new Date(Date.now() + 7 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
      orderNo: jwData.orderNo || "SO-2026-1045",
      customer: jwData.customer || "ABC Fashion",
      jobWorker: jwData.jobWorker || "Raj Stitching",
      jobWorkerId: jwData.jobWorkerId || "JWK-001",
      freight: jwData.freight || "To Pay",
      factoryChallan: jwData.factoryChallan || "",
      remark: jwData.remark || "",
      item: primaryItem,
      lotNo: primaryLotNo,
      stage: primaryStage,
      process: primaryProcess,
      lrNo: primaryLRNo,
      transport: primaryTransport,
      meter: totalMeters,
      netMeter: totalNetMeters,
      quantity: totalQty,
      sentQty: totalQty,
      rate: rate,
      totalAmount: totalAmount,
      items: items,
      status: "In Progress",
      receivedGoodQty: 0,
      rejectedQty: 0,
      damagedQty: 0,
      pendingQty: totalQty,
      qcStatus: "Pending Return"
    };

    this.data.jobWorks.unshift(newJW);

    // Update Lot Timeline for each Lot in items
    items.forEach(it => {
      const lot = this.data.lots.find(l => l.lotNo === it.lotNo);
      if (lot) {
        lot.currentProcess = `${it.process || newJW.process} (at ${newJW.jobWorker})`;
        lot.timeline.push({
          date: new Date().toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }),
          time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
          stage: `Job Work: ${it.process || newJW.process}`,
          qty: it.netMeter || it.meter || totalQty,
          operator: this.data.currentUser.name,
          note: `Assign No: ${newJW.assignNo} (${newJW.id}) | Worker: ${newJW.jobWorker} | Challan: ${newJW.factoryChallan || 'N/A'}`
        });
      }
    });

    this.logActivity(`Assigned Job Work #${newJW.assignNo} (${id}) to ${newJW.jobWorker} - ${items.length} items (${totalNetMeters || totalQty} mtrs)`, "Job Work", id);
    this.saveState();
    return newJW;
  }

  receiveJobWorkInward(jwId, inward) {
    const jw = this.data.jobWorks.find(j => j.id === jwId);
    if (jw) {
      const goodQty = Number(inward.goodQty);
      const rejectedQty = Number(inward.rejectedQty || 0);
      const damagedQty = Number(inward.damagedQty || 0);
      
      jw.receivedGoodQty += goodQty;
      jw.rejectedQty += rejectedQty;
      jw.damagedQty += damagedQty;
      jw.pendingQty = Math.max(0, jw.sentQty - jw.receivedGoodQty - jw.rejectedQty - jw.damagedQty);
      
      if (jw.pendingQty === 0) {
        jw.status = "Completed";
        jw.qcStatus = "Ready for QC";
      } else {
        jw.status = "Partially Received";
        jw.qcStatus = "Partially Received";
      }

      // Add to Lot Timeline
      const lot = this.data.lots.find(l => l.lotNo === jw.lotNo);
      if (lot) {
        lot.currentQty = jw.receivedGoodQty;
        lot.timeline.push({
          date: new Date().toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }),
          time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
          stage: `Inward from ${jw.jobWorker}`,
          qty: goodQty,
          operator: this.data.currentUser.name,
          note: `Received ${goodQty} good, ${rejectedQty} rejected, ${damagedQty} damaged`
        });
      }

      this.logActivity(`Received Job Work Inward: ${jw.id} (${goodQty} good pcs)`, "Job Work Inward", jw.id);
      this.saveState();
    }
  }

  // --- 8. QUALITY CHECK (QC) ---
  recordQualityCheck(qcData) {
    const id = `QC-2026-${String(this.data.qualityChecks.length + 313).padStart(4, '0')}`;
    const passedQty = Number(qcData.passedQty);
    const reworkQty = Number(qcData.reworkQty || 0);
    const rejectedQty = Number(qcData.rejectedQty || 0);

    const newQC = {
      id,
      jobWorkNo: qcData.jobWorkNo,
      lotNo: qcData.lotNo,
      item: qcData.item,
      receivedQty: Number(qcData.receivedQty),
      qcChecked: passedQty + reworkQty + rejectedQty,
      passedQty,
      reworkQty,
      rejectedQty,
      qcPerson: this.data.currentUser.name,
      qcDate: qcData.qcDate || new Date().toISOString().split('T')[0],
      remarks: qcData.remarks || "Standard QC inspection completed.",
      status: reworkQty > 0 || rejectedQty > 0 ? "Partially Passed" : "Passed"
    };

    this.data.qualityChecks.unshift(newQC);

    // Update finished goods inventory if passed
    const itm = this.data.items.find(i => i.name.toLowerCase() === qcData.item.toLowerCase());
    if (itm) {
      itm.inwardStock += passedQty;
      itm.currentStock += passedQty;

      this.data.itemLedger.unshift({
        date: newQC.qcDate,
        ref: id,
        item: itm.name,
        type: "Production Inward (QC Passed)",
        inward: passedQty,
        outward: 0,
        balance: itm.currentStock,
        user: this.data.currentUser.name,
        lot: qcData.lotNo
      });
    }

    // Update Lot
    const lot = this.data.lots.find(l => l.lotNo === qcData.lotNo);
    if (lot) {
      lot.currentProcess = "QC Completed - Ready for Dispatch";
      lot.status = "Ready for Dispatch";
      lot.timeline.push({
        date: new Date().toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }),
        time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
        stage: "Quality Check Completed",
        qty: passedQty,
        operator: this.data.currentUser.name,
        note: `QC Passed: ${passedQty} pcs, Rework: ${reworkQty}, Rejected: ${rejectedQty}`
      });
    }

    this.logActivity(`QC Inspection Completed: ${id} (${passedQty} pcs passed for ${qcData.lotNo})`, "Quality Check", id);
    this.saveState();
    return newQC;
  }

  // --- 9. DISPATCH MANAGEMENT ---
  createDispatch(dispatchData) {
    const count = this.data.dispatches.length + 513;
    const id = `DO-2026-${String(count).padStart(4, '0')}`;
    const orderDispatchNo = dispatchData.orderDispatchNo || String(this.data.dispatches.length + 4).padStart(4, '0');
    const billNo = dispatchData.billNo || `BILL-2026-${String(this.data.invoices.length + 923).padStart(3, '0')}`;
    const invoiceNo = `INV-2026-${String(this.data.invoices.length + 923).padStart(4, '0')}`;

    const items = dispatchData.items && dispatchData.items.length > 0 ? dispatchData.items : [{
      baleNo: "BALE-01",
      lotNo: dispatchData.lotNo || "dh/0000/0008/01",
      item: dispatchData.item || "100% Combed Cotton Fabric 180 GSM",
      availableMeter: 5000,
      meter: Number(dispatchData.quantity || 1200),
      netMeter: Number(dispatchData.quantity || 1200),
      rate: Number(dispatchData.rate || 145),
      amount: Number((dispatchData.quantity || 1200) * (dispatchData.rate || 145)),
      gst: Number((dispatchData.quantity || 1200) * (dispatchData.rate || 145) * 0.18),
      totalAmount: Number((dispatchData.quantity || 1200) * (dispatchData.rate || 145) * 1.18)
    }];

    const totalMeters = items.reduce((acc, it) => acc + Number(it.netMeter || it.meter || 0), 0);
    const subtotalAmount = items.reduce((acc, it) => acc + Number(it.amount || 0), 0);
    const totalGst = items.reduce((acc, it) => acc + Number(it.gst || 0), 0);
    const grandTotal = items.reduce((acc, it) => acc + Number(it.totalAmount || (it.amount + it.gst)), 0);

    const newDispatch = {
      id,
      orderDispatchNo,
      billNo,
      orderNo: dispatchData.orderNo || `SO-2026-${orderDispatchNo}`,
      customer: dispatchData.customer,
      customerId: dispatchData.customerId || "CUST-001",
      invoiceNo,
      dispatchDate: dispatchData.date || dispatchData.dispatchDate || new Date().toISOString().split('T')[0],
      transport: dispatchData.transport || dispatchData.transporter || "V-Trans Logistics India",
      transporter: dispatchData.transport || dispatchData.transporter || "V-Trans Logistics India",
      vehicleNo: dispatchData.vehicleNo || "MH-04-GP-8842",
      lrNumber: dispatchData.lrNumber || `LR-VT-${Date.now().toString().slice(-6)}`,
      quantity: totalMeters,
      invoiceAmount: grandTotal,
      subtotalAmount,
      totalGst,
      status: dispatchData.status || "Pending",
      items: items
    };

    this.data.dispatches.unshift(newDispatch);

    // Deduct stock from finished goods inventory for each dispatched bale
    items.forEach(bale => {
      const itm = this.data.items.find(i => i.name.toLowerCase() === bale.item.toLowerCase());
      if (itm) {
        itm.outwardStock += Number(bale.netMeter || bale.meter || 0);
        itm.currentStock = Math.max(0, itm.currentStock - Number(bale.netMeter || bale.meter || 0));

        this.data.itemLedger.unshift({
          date: newDispatch.dispatchDate,
          ref: id,
          item: itm.name,
          type: "Sales Dispatch",
          inward: 0,
          outward: Number(bale.netMeter || bale.meter || 0),
          balance: itm.currentStock,
          user: this.data.currentUser.name,
          lot: bale.lotNo || "LOT-DISPATCH"
        });
      }
    });

    // Create Invoice & Increase Customer Outstanding
    this.data.invoices.unshift({
      invoiceNo,
      orderNo: newDispatch.orderNo,
      customer: dispatchData.customer,
      date: newDispatch.dispatchDate,
      amount: grandTotal,
      paidAmount: 0,
      balanceAmount: grandTotal,
      dueDate: new Date(Date.now() + 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
      status: "Unpaid"
    });

    const cust = this.data.customers.find(c => c.name.toLowerCase() === dispatchData.customer.toLowerCase());
    if (cust) {
      cust.outstanding += grandTotal;
    }

    // Update Sales Order Status if linked
    if (newDispatch.orderNo) {
      const so = this.data.salesOrders.find(s => s.id === newDispatch.orderNo || s.pchNo === newDispatch.orderNo);
      if (so) {
        so.status = "Dispatched";
        so.stage = "Dispatched";
      }
    }

    this.logActivity(`Generated Order Dispatch: #${orderDispatchNo} (${items.length} Bales, ${totalMeters} M for ${dispatchData.customer})`, "Dispatch", id);
    this.saveState();
    return newDispatch;
  }

  // --- 10. CUSTOMER SALES INVOICES & SETTLEMENT ---
  createCustomerSalesInvoice(invData) {
    const nextNum = this.data.invoices.length + 925;
    const invoiceNo = invData.invoiceNo || `INV-2026-${String(nextNum).padStart(4, '0')}`;
    
    const items = invData.items && invData.items.length > 0 ? invData.items : [{
      item: "100% Combed Cotton Fabric 180 GSM",
      hsn: "5208",
      baleNo: "BALE-01",
      lotNo: "LOT-2026-00145",
      qty: 2500,
      unit: "Meters",
      rate: 145,
      discount: 0,
      taxableAmount: 362500,
      gstRate: 18,
      gstAmount: 65250,
      totalAmount: 427750
    }];

    const taxableTotal = items.reduce((acc, it) => acc + Number(it.taxableAmount || (it.qty * it.rate)), 0);
    const totalGst = items.reduce((acc, it) => acc + Number(it.gstAmount || 0), 0);
    const grandTotal = items.reduce((acc, it) => acc + Number(it.totalAmount || 0), 0);

    const customerObj = this.data.customers.find(c => c.name.toLowerCase() === (invData.customer || '').toLowerCase());

    const newInvoice = {
      invoiceNo,
      orderNo: invData.dispatchRef || invData.orderNo || "N/A",
      dispatchRef: invData.dispatchRef || "N/A",
      customer: invData.customer,
      customerId: customerObj ? customerObj.id : (invData.customerId || "CUST-001"),
      customerGstin: customerObj ? customerObj.gstin : (invData.customerGstin || "27AABCF1234F1Z5"),
      customerAddress: customerObj ? `${customerObj.address}, ${customerObj.city}, ${customerObj.state}` : (invData.customerAddress || ""),
      date: invData.date || new Date().toISOString().split('T')[0],
      dueDate: invData.dueDate || new Date(Date.now() + 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
      paymentTerms: invData.paymentTerms || "30 Days",
      placeOfSupply: invData.placeOfSupply || (customerObj ? customerObj.state : "Maharashtra (27)"),
      items,
      taxableTotal,
      totalGst,
      amount: grandTotal,
      paidAmount: Number(invData.paidAmount || 0),
      balanceAmount: grandTotal - Number(invData.paidAmount || 0),
      status: Number(invData.paidAmount || 0) >= grandTotal ? "Paid" : (Number(invData.paidAmount || 0) > 0 ? "Partially Paid" : "Unpaid"),
      notes: invData.notes || "Payment is due within payment terms. Subject to Mumbai jurisdiction."
    };

    this.data.invoices.unshift(newInvoice);

    // Increase customer outstanding
    if (customerObj) {
      customerObj.outstanding += newInvoice.balanceAmount;
    }

    this.logActivity(`Created Sales Tax Invoice: ${invoiceNo} for ${invData.customer} (₹${grandTotal.toLocaleString('en-IN')})`, "Invoice", invoiceNo);
    this.saveState();
    return newInvoice;
  }

  deleteCustomerInvoice(invoiceNo) {
    const idx = this.data.invoices.findIndex(i => i.invoiceNo === invoiceNo);
    if (idx !== -1) {
      const inv = this.data.invoices[idx];
      const cust = this.data.customers.find(c => c.name.toLowerCase() === inv.customer.toLowerCase());
      if (cust) {
        cust.outstanding = Math.max(0, cust.outstanding - inv.balanceAmount);
      }
      this.data.invoices.splice(idx, 1);
      this.logActivity(`Deleted Sales Invoice: ${invoiceNo}`, "Invoice", invoiceNo);
      this.saveState();
      return true;
    }
    return false;
  }

  recordPayment(paymentData) {
    const id = `REC-2026-${String(this.data.payments.length + 342).padStart(4, '0')}`;
    const amount = Number(paymentData.amount);

    const newPayment = {
      id,
      customer: paymentData.customer,
      invoiceNo: paymentData.invoiceNo || "N/A",
      date: paymentData.date || new Date().toISOString().split('T')[0],
      amount,
      mode: paymentData.mode || "Bank Transfer (NEFT)",
      refNo: paymentData.refNo || `TXN-${Date.now().toString().slice(-6)}`,
      remarks: paymentData.remarks || "Payment received against outstanding invoices"
    };

    this.data.payments.unshift(newPayment);

    // Reduce Customer Outstanding
    const cust = this.data.customers.find(c => c.name.toLowerCase() === paymentData.customer.toLowerCase());
    if (cust) {
      cust.outstanding = Math.max(0, cust.outstanding - amount);
    }

    // Update invoice balance if linked
    if (paymentData.invoiceNo) {
      const inv = this.data.invoices.find(i => i.invoiceNo === paymentData.invoiceNo);
      if (inv) {
        inv.paidAmount += amount;
        inv.balanceAmount = Math.max(0, inv.amount - inv.paidAmount);
        inv.status = inv.balanceAmount === 0 ? "Paid" : "Partially Paid";
      }
    }

    this.logActivity(`Received Payment: ₹${amount.toLocaleString('en-IN')} from ${paymentData.customer} (${newPayment.mode})`, "Settlement", id);
    this.saveState();
    return newPayment;
  }

  // --- 11. STANDALONE DISCOUNT QR VOUCHERS & SINGLE-USE REDEMPTION ---
  addDiscountCoupon(couponData) {
    if (!this.data.discountCoupons) this.data.discountCoupons = [];

    const id = `CPN-${String(this.data.discountCoupons.length + 1).padStart(3, '0')}`;
    const code = couponData.code ? couponData.code.toUpperCase().trim() : `SAVE${couponData.amount}-${Math.random().toString(36).substring(2, 6).toUpperCase()}`;

    const newCoupon = {
      id,
      code,
      type: couponData.type || "fixed",
      amount: Number(couponData.amount || 100),
      title: couponData.title || `Special ₹${couponData.amount} Off Discount`,
      minBill: Number(couponData.minBill || 0),
      validTill: couponData.validTill || new Date(Date.now() + 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
      color: couponData.color || "#0f172a",
      usageType: couponData.usageType || "single", // 'single' (auto-expires on scan) | 'multi'
      status: "Active",
      createdAt: new Date().toISOString().split('T')[0],
      timesScanned: 0,
      redeemedByPhone: null,
      redeemedAt: null,
      claimId: null
    };

    this.data.discountCoupons.unshift(newCoupon);
    this.logActivity(`Created Discount QR: ${code} (₹${newCoupon.amount} Off - ${newCoupon.usageType === 'single' ? 'Single Use' : 'Multi Use'})`, "QR Discount", id);
    this.saveState();
    return newCoupon;
  }

  deleteDiscountCoupon(id) {
    if (!this.data.discountCoupons) return;
    const c = this.data.discountCoupons.find(cpn => cpn.id === id);
    this.data.discountCoupons = this.data.discountCoupons.filter(cpn => cpn.id !== id);
    if (c) {
      this.logActivity(`Deleted Discount Voucher: ${c.code}`, "QR Discount", id);
    }
    this.saveState();
  }

  expireCoupon(id) {
    if (!this.data.discountCoupons) return;
    const c = this.data.discountCoupons.find(cpn => cpn.id === id);
    if (c) {
      c.status = "Expired";
      this.logActivity(`Manually Expired Discount Voucher: ${c.code}`, "QR Discount", id);
      this.saveState();
    }
  }

  reactivateCoupon(id) {
    if (!this.data.discountCoupons) return;
    const c = this.data.discountCoupons.find(cpn => cpn.id === id);
    if (c) {
      c.status = "Active";
      c.timesScanned = 0;
      c.redeemedByPhone = null;
      c.redeemedAt = null;
      c.claimId = null;
      this.logActivity(`Reactivated Discount Voucher: ${c.code}`, "QR Discount", id);
      this.saveState();
    }
  }

  redeemDiscountCoupon(codeOrPayload, phoneNumber = null) {
    if (!this.data.discountCoupons) this.data.discountCoupons = [];
    let cleanCode = String(codeOrPayload || "").trim();
    try {
      if (typeof codeOrPayload === 'string' && codeOrPayload.startsWith('{')) {
        const parsed = JSON.parse(codeOrPayload);
        cleanCode = parsed.code || codeOrPayload;
      }
    } catch(e) {}

    const coupon = this.data.discountCoupons.find(c => c.code.toLowerCase() === cleanCode.toLowerCase().trim());
    
    if (!coupon) {
      return {
        success: false,
        reason: "not_found",
        message: `Voucher code "${cleanCode}" not found in system.`
      };
    }

    // Check if already redeemed / expired (Single-use auto-expiry)
    if (coupon.status === "Redeemed / Expired" || (coupon.usageType === "single" && coupon.timesScanned >= 1)) {
      return {
        success: false,
        reason: "already_redeemed",
        coupon: coupon,
        message: `This QR voucher (${coupon.code}) has already been redeemed by ${coupon.redeemedByPhone || 'a customer'} on ${coupon.redeemedAt || 'earlier session'} and is now expired.`
      };
    }

    // Check if expired by date
    const todayStr = new Date().toISOString().split('T')[0];
    if (coupon.validTill && coupon.validTill < todayStr) {
      coupon.status = "Expired";
      this.saveState();
      return {
        success: false,
        reason: "expired",
        coupon: coupon,
        message: `This QR voucher (${coupon.code}) expired on ${coupon.validTill}.`
      };
    }

    if (coupon.status === "Expired") {
      return {
        success: false,
        reason: "expired",
        coupon: coupon,
        message: `This QR voucher (${coupon.code}) has been deactivated or expired.`
      };
    }

    // Execute successful redemption
    const now = new Date();
    const formattedTimestamp = now.toLocaleDateString('en-IN', { day: '2-digit', month: 'short', year: 'numeric' }) + ' ' + now.toLocaleTimeString('en-IN', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true });
    const claimId = `CLM-${now.getFullYear()}-${String(Math.floor(Math.random() * 9000 + 1000))}`;
    const cleanPhone = phoneNumber ? String(phoneNumber).trim() : "+91 98201 12345";

    coupon.timesScanned = (coupon.timesScanned || 0) + 1;
    coupon.redeemedByPhone = cleanPhone;
    coupon.redeemedAt = formattedTimestamp;
    coupon.claimId = claimId;

    if (coupon.usageType === "single" || !coupon.usageType) {
      coupon.status = "Redeemed / Expired";
    }

    this.logActivity(`Redeemed Discount Voucher ${coupon.code} by ${cleanPhone} (Claim ID: ${claimId})`, "QR Discount", coupon.id);
    this.saveState();

    return {
      success: true,
      coupon: coupon,
      claimId: claimId,
      phone: cleanPhone,
      timestamp: formattedTimestamp,
      message: `Discount voucher verified and redeemed successfully!`
    };
  }

  getCouponAnalytics() {
    const list = this.data.discountCoupons || [];
    const totalIssued = list.length;
    const active = list.filter(c => c.status === "Active").length;
    const redeemed = list.filter(c => c.status === "Redeemed / Expired" || (c.timesScanned > 0)).length;
    const expired = list.filter(c => c.status === "Expired").length;
    const totalSavingsDisbursed = list
      .filter(c => c.status === "Redeemed / Expired" || (c.timesScanned > 0))
      .reduce((sum, c) => sum + (c.type === "fixed" ? c.amount : 500), 0);

    return {
      totalIssued,
      active,
      redeemed,
      expired,
      totalSavingsDisbursed
    };
  }

  // --- KPI & Summary Metrics Calculator ---
  getDashboardStats() {
    if (!this.data.discountCoupons) this.data.discountCoupons = JSON.parse(JSON.stringify(INITIAL_DATA.discountCoupons || []));
    const totalOrders = this.data.salesOrders.length;
    const prodInProgress = this.data.salesOrders.filter(o => o.status === "In Production" || o.status === "Material Planned").length;
    const pendingJobWork = this.data.jobWorks.filter(j => j.status === "Assigned" || j.status === "In Progress").length;
    const readyForDispatch = this.data.salesOrders.filter(o => o.status === "Ready for Dispatch").length;
    const totalStock = this.data.items.reduce((acc, itm) => acc + itm.currentStock, 0);
    const custOutstanding = this.data.customers.reduce((acc, c) => acc + c.outstanding, 0);
    const vendorOutstanding = this.data.vendors.reduce((acc, v) => acc + v.outstanding, 0);
    const todayDispatch = this.data.dispatches.reduce((acc, d) => acc + d.invoiceAmount, 0);

    const rawStock = this.data.items.filter(i => i.type === "Raw Material" || i.type === "Accessories").reduce((acc, i) => acc + i.currentStock, 0);
    const finishedStock = this.data.items.filter(i => i.type === "Finished Goods").reduce((acc, i) => acc + i.currentStock, 0);
    const wipStock = 8200; // Work in progress estimated units

    return {
      totalOrders,
      prodInProgress,
      pendingJobWork,
      readyForDispatch,
      totalStock,
      custOutstanding,
      vendorOutstanding,
      todayDispatch,
      rawStock,
      wipStock,
      finishedStock
    };
  }
}

// Global single instance
const ERPState = new ERPStateManager();


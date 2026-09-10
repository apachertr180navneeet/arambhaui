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

  // --- 1. CUSTOMERS ---
  addCustomer(customer) {
    const id = `CUST-${String(this.data.customers.length + 1).padStart(3, '0')}`;
    const newCust = {
      id,
      ...customer,
      outstanding: Number(customer.openingBalance || 0),
      totalOrders: 0,
      createdAt: new Date().toISOString().split('T')[0],
      status: customer.status || "Active"
    };
    this.data.customers.unshift(newCust);
    this.logActivity(`Added new customer: ${newCust.name}`, "Customer Master", id);
    this.saveState();
    return newCust;
  }

  updateCustomer(id, updatedData) {
    const idx = this.data.customers.findIndex(c => c.id === id);
    if (idx !== -1) {
      this.data.customers[idx] = { ...this.data.customers[idx], ...updatedData };
      this.logActivity(`Updated customer details: ${this.data.customers[idx].name}`, "Customer Master", id);
      this.saveState();
    }
  }

  deleteCustomer(id) {
    const cust = this.data.customers.find(c => c.id === id);
    if (cust) {
      this.data.customers = this.data.customers.filter(c => c.id !== id);
      this.logActivity(`Deleted customer: ${cust.name}`, "Customer Master", id);
      this.saveState();
    }
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

  // --- 6. JOB WORK & OUTWARD / INWARD ---
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
    const c = this.data.discountCoupons.find(cpn => String(cpn.id) === String(id) || cpn.code === id);
    this.data.discountCoupons = this.data.discountCoupons.filter(cpn => String(cpn.id) !== String(id) && cpn.code !== id);
    if (c) {
      this.logActivity(`Deleted Discount Voucher: ${c.code}`, "QR Discount", id);
    }
    this.saveState();
  }

  expireCoupon(id) {
    if (!this.data.discountCoupons) return;
    const c = this.data.discountCoupons.find(cpn => String(cpn.id) === String(id) || cpn.code === id);
    if (c) {
      c.status = "Redeemed / Expired";
      c.isRedeemed = true;
      this.logActivity(`Manually Expired Discount Voucher: ${c.code}`, "QR Discount", id);
      this.saveState();
    }
  }

  reactivateCoupon(id) {
    if (!this.data.discountCoupons) return;
    const c = this.data.discountCoupons.find(cpn => String(cpn.id) === String(id) || cpn.code === id);
    if (c) {
      c.status = "Active";
      c.isRedeemed = false;
      c.timesScanned = 0;
      c.redeemedByPhone = null;
      c.redeemedAt = null;
      c.claimId = null;
      this.logActivity(`Reactivated Discount Voucher: ${c.code}`, "QR Discount", id);
      this.saveState();
    }
  }

  redeemDiscountCoupon(codeOrPayload, phoneNumber = null, orderBill = 3500) {
    if (!this.data.discountCoupons) this.data.discountCoupons = [];
    let cleanCode = String(codeOrPayload || "").trim();
    try {
      if (typeof codeOrPayload === 'string' && codeOrPayload.startsWith('{')) {
        const parsed = JSON.parse(codeOrPayload);
        cleanCode = parsed.code || codeOrPayload;
      }
    } catch(e) {}

    const coupon = this.data.discountCoupons.find(c => (c.code || "").toLowerCase() === cleanCode.toLowerCase().trim());
    
    if (!coupon) {
      return {
        success: false,
        reason: "not_found",
        message: `Voucher code "${cleanCode}" not found in database.`
      };
    }

    // Check if already redeemed / expired (Single-use auto-expiry)
    if (coupon.isRedeemed || coupon.status === "Redeemed / Expired" || coupon.status === "Redeemed") {
      return {
        success: false,
        reason: "already_redeemed",
        coupon: coupon,
        message: `This single-use QR voucher (${coupon.code}) has already been redeemed by ${coupon.redeemedByPhone || 'customer'} on ${coupon.redeemedAt || 'earlier transaction'} and is now permanently expired.`
      };
    }

    // Check if expired by date
    const todayStr = new Date().toISOString().split('T')[0];
    const expiryDate = coupon.validTill || coupon.validUntil;
    if (expiryDate && expiryDate < todayStr) {
      coupon.status = "Expired";
      this.saveState();
      return {
        success: false,
        reason: "expired",
        coupon: coupon,
        message: `This QR voucher (${coupon.code}) expired on ${expiryDate} and can no longer be used.`
      };
    }

    const claimId = `CLM-${Math.random().toString(36).substring(2, 8).toUpperCase()}`;
    const timestampStr = new Date().toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit', hour12: true });

    coupon.isRedeemed = true;
    coupon.status = "Redeemed / Expired";
    coupon.timesScanned = (coupon.timesScanned || 0) + 1;
    coupon.redeemedByPhone = phoneNumber || "+91 98201 12345";
    coupon.redeemedAt = timestampStr;
    coupon.claimId = claimId;

    this.logActivity(`Redeemed & Expired Discount QR: ${coupon.code} for ${coupon.redeemedByPhone}`, "QR Discount", coupon.code);
    this.saveState();

    return {
      success: true,
      coupon: coupon,
      claimId: claimId,
      timestamp: timestampStr,
      phone: coupon.redeemedByPhone,
      message: `Discount voucher ${coupon.code} verified & claimed! Single-use voucher is now expired.`
    };
  }

  getCouponAnalytics() {
    const list = this.data.discountCoupons || [];
    const active = list.filter(c => c.status === "Active" && !c.isRedeemed).length;
    const redeemed = list.filter(c => c.status === "Redeemed / Expired" || c.isRedeemed).length;
    const expired = list.filter(c => c.status === "Expired" && !c.isRedeemed).length;
    const totalSavingsDisbursed = list
      .filter(c => c.status === "Redeemed / Expired" || c.isRedeemed)
      .reduce((sum, c) => sum + Number(c.amount || c.discountAmount || 500), 0);

    return {
      totalIssued: list.length,
      active,
      redeemed,
      expired,
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


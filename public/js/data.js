/* ==========================================================================
   INITIAL GARMENT ERP DATA SCHEMA & DEFAULTS
   Clean schema initialized dynamically from Laravel Backend API
   ========================================================================== */

const INITIAL_DATA = {
  company: {
    name: "FashionWorks Pvt. Ltd.",
    brand: "GarmentERP",
    tagline: "Manufacturing Management System",
    cin: "U18101MH2018PTC312450",
    gstin: "27AABCF4590K1ZX",
    pan: "AABCF4590K",
    address: "Plot No. 42-45, Apparel Park, Phase II, MIDC Industrial Area, Tiruppur / Mumbai, Maharashtra - 400093",
    email: "info@fashionworks.co.in",
    phone: "+91 22 4920 8800 / +91 98201 55432",
    currency_symbol: "₹",
    financial_year: "2026-2027",
    bank: {
      name: "HDFC Bank Ltd.",
      branch: "MIDC Industrial Branch",
      accountNo: "50200045892110",
      ifsc: "HDFC0000458"
    }
  },

  currentUser: {
    id: "USR-001",
    name: "Admin User",
    email: "admin@garmenterp.com",
    role: "Administrator",
    avatar: "AU",
    department: "Executive Management"
  },

  // Dynamic Collections (Populated live via Laravel Database API)
  customers: [],
  vendors: [],
  jobWorkers: [],
  items: [],
  units: [
    { id: 1, name: "PAIR", code: "PAIR", parentId: null, parentName: null, conversionFactor: null, symbol: "pr", status: "Active" },
    { id: 2, name: "PCS", code: "PCS", parentId: null, parentName: null, conversionFactor: null, symbol: "pcs", status: "Active" },
    { id: 3, name: "KG", code: "KG", parentId: null, parentName: null, conversionFactor: null, symbol: "kg", status: "Active" },
    { id: 4, name: "gm", code: "gm", parentId: 3, parentName: "KG", conversionFactor: 1000.00, symbol: "gm", status: "Active" }
  ],
  processes: [
    "Cutting",
    "Stitching",
    "Embroidery",
    "Printing",
    "Dyeing",
    "Washing",
    "Finishing",
    "Quality Check",
    "Packing"
  ],
  warehouses: [
    { id: "WH-01", name: "Main Raw Material Store - Unit 1", location: "Building A, Ground Floor", manager: "Ramesh Thorat" },
    { id: "WH-02", name: "Finished Goods Hub - Unit 2", location: "Building B, 1st Floor", manager: "Ganesh Patil" },
    { id: "WH-03", name: "Job Work Dispatch Yard", location: "Gate 3 Logistics Area", manager: "Sunil Shinde" }
  ],

  salesOrders: [],
  purchaseOrders: [],
  jobWorks: [],
  productionOrders: [],
  qualityChecks: [],
  lotTracking: [],
  dispatchChallans: [],
  invoices: [],
  payments: [],
  itemLedger: [],
  users: [],
  roles: [
    { role: "Administrator", permissions: { view: true, create: true, edit: true, delete: true, approve: true, export: true } },
    { role: "Production Supervisor", permissions: { view: true, create: true, edit: true, delete: false, approve: true, export: true } },
    { role: "Store Manager", permissions: { view: true, create: true, edit: true, delete: false, approve: false, export: true } },
    { role: "Accounts Manager", permissions: { view: true, create: true, edit: true, delete: false, approve: true, export: true } },
    { role: "Dispatch Manager", permissions: { view: true, create: true, edit: false, delete: false, approve: false, export: true } }
  ],
  activityLogs: [],
  notifications: [],
  discountCoupons: [],
  qrVouchers: []
};

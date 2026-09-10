/* ==========================================================================
   INITIAL GARMENT ERP DATASET (FashionWorks Pvt. Ltd.)
   Realistic Indian B2B Manufacturing Records
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

  // 1. Customer Master
  customers: [
    {
      id: "CUST-001",
      name: "ABC Fashion",
      companyName: "ABC Fashion Apparels Pvt Ltd",
      contactPerson: "Rajesh Khanna",
      mobile: "+91 98201 12345",
      email: "purchase@abcfashion.com",
      gstin: "27AAACA1234A1Z5",
      city: "Mumbai",
      state: "Maharashtra",
      pincode: "400013",
      address: "Unit 12, Phoenix Mills Compound, Lower Parel",
      creditLimit: 2500000,
      paymentTerms: "30 Days",
      outstanding: 450000,
      status: "Active",
      totalOrders: 28,
      createdAt: "2025-04-10"
    },
    {
      id: "CUST-002",
      name: "Urban Wear",
      companyName: "Urban Wear Retail Chain LLP",
      contactPerson: "Priya Sharma",
      mobile: "+91 98112 55678",
      email: "orders@urbanwear.in",
      gstin: "07AABCU9876B1Z2",
      city: "New Delhi",
      state: "Delhi",
      pincode: "110020",
      address: "B-42, Okhla Industrial Area, Phase 1",
      creditLimit: 3000000,
      paymentTerms: "45 Days",
      outstanding: 620000,
      status: "Active",
      totalOrders: 34,
      createdAt: "2025-05-18"
    },
    {
      id: "CUST-003",
      name: "StyleHub Retail",
      companyName: "StyleHub E-Commerce Ventures",
      contactPerson: "Amit Patel",
      mobile: "+91 97234 88990",
      email: "sourcing@stylehub.com",
      gstin: "24AABCS4567C1Z8",
      city: "Ahmedabad",
      state: "Gujarat",
      pincode: "380015",
      address: "SG Highway, Prahlad Nagar",
      creditLimit: 1500000,
      paymentTerms: "15 Days",
      outstanding: 280000,
      status: "Active",
      totalOrders: 19,
      createdAt: "2025-08-01"
    },
    {
      id: "CUST-004",
      name: "Reliance Garments",
      companyName: "Reliance Garments & Textiles",
      contactPerson: "Suresh Menon",
      mobile: "+91 98450 33211",
      email: "procure@reliancegarments.com",
      gstin: "29AABCR8890D1Z4",
      city: "Bengaluru",
      state: "Karnataka",
      pincode: "560001",
      address: "Prestige Meridian, MG Road",
      creditLimit: 5000000,
      paymentTerms: "60 Days",
      outstanding: 350000,
      status: "Active",
      totalOrders: 42,
      createdAt: "2025-02-12"
    },
    {
      id: "CUST-005",
      name: "Modern Clothing",
      companyName: "Modern Clothing Lifestyle Store",
      contactPerson: "Vikram Singhania",
      mobile: "+91 98300 77654",
      email: "orders@modernclothing.co.in",
      gstin: "19AABCM6789E1Z1",
      city: "Kolkata",
      state: "West Bengal",
      pincode: "700016",
      address: "Park Street Business Center",
      creditLimit: 2000000,
      paymentTerms: "30 Days",
      outstanding: 150000,
      status: "Active",
      totalOrders: 15,
      createdAt: "2025-09-05"
    }
  ],

  // 2. Vendor Master
  vendors: [
    {
      id: "VND-001",
      name: "Shree Fabrics",
      contactPerson: "Ramesh Jain",
      mobile: "+91 98251 44321",
      email: "sales@shreefabrics.in",
      gstin: "24AABCS1122F1Z9",
      city: "Surat",
      state: "Gujarat",
      category: "Fabric Supplier",
      paymentTerms: "30 Days",
      outstanding: 380000,
      status: "Active",
      address: "Ring Road Textile Market, Surat"
    },
    {
      id: "VND-002",
      name: "ABC Textile Supplier",
      contactPerson: "Mohan Lal",
      mobile: "+91 94220 88765",
      email: "info@abctextiles.com",
      gstin: "27AABCA3344G1Z3",
      city: "Ichalkaranji",
      state: "Maharashtra",
      category: "Cotton & Denim Mill",
      paymentTerms: "45 Days",
      outstanding: 290000,
      status: "Active",
      address: "Textile City Industrial Estate"
    },
    {
      id: "VND-003",
      name: "Royal Threads",
      contactPerson: "Kavita Shah",
      mobile: "+91 98190 22334",
      email: "sales@royalthreads.com",
      gstin: "27AABCR5566H1Z7",
      city: "Mumbai",
      state: "Maharashtra",
      category: "Sewing Threads & Yarns",
      paymentTerms: "15 Days",
      outstanding: 85000,
      status: "Active",
      address: "Dadar West, Mumbai"
    },
    {
      id: "VND-004",
      name: "Modern Accessories",
      contactPerson: "Dinesh Agarwal",
      mobile: "+91 98401 99887",
      email: "contact@modernacc.in",
      gstin: "33AABCM7788I1Z1",
      city: "Tiruppur",
      state: "Tamil Nadu",
      category: "Buttons, Zippers & Labels",
      paymentTerms: "30 Days",
      outstanding: 110000,
      status: "Active",
      address: "Avinashi Road, Tiruppur"
    },
    {
      id: "VND-005",
      name: "Perfect Packaging",
      contactPerson: "Naveen Gupta",
      mobile: "+91 98100 66554",
      email: "boxes@perfectpack.in",
      gstin: "07AABCP9900J1Z5",
      city: "New Delhi",
      state: "Delhi",
      category: "Corrugated Boxes & Polybags",
      paymentTerms: "30 Days",
      outstanding: 55000,
      status: "Active",
      address: "Mayapuri Industrial Area Phase II"
    }
  ],

  // 3. Job Worker Master
  jobWorkers: [
    {
      id: "JWK-001",
      name: "Raj Stitching",
      contactPerson: "Rajendra Yadav",
      mobile: "+91 98210 99881",
      process: "Stitching",
      rate: 25,
      rateUnit: "Piece",
      city: "Bhiwandi",
      capacityPerDay: 1500,
      outstanding: 125000,
      status: "Active",
      address: "Kalyan Road, Bhiwandi, Thane"
    },
    {
      id: "JWK-002",
      name: "Perfect Embroidery",
      contactPerson: "Sunil Sharma",
      mobile: "+91 98670 44332",
      process: "Embroidery",
      rate: 18,
      rateUnit: "Piece",
      city: "Surat",
      capacityPerDay: 2000,
      outstanding: 78000,
      status: "Active",
      address: "Pandesara GIDC, Surat"
    },
    {
      id: "JWK-003",
      name: "ColorPrint Works",
      contactPerson: "Anand Verma",
      mobile: "+91 98920 11223",
      process: "Printing",
      rate: 15,
      rateUnit: "Piece",
      city: "Mumbai",
      capacityPerDay: 3000,
      outstanding: 95000,
      status: "Active",
      address: "Goregaon East Industrial Area"
    },
    {
      id: "JWK-004",
      name: "Shree Washing",
      contactPerson: "Baldev Singh",
      mobile: "+91 98140 77889",
      process: "Washing",
      rate: 12,
      rateUnit: "Piece",
      city: "Ludhiana",
      capacityPerDay: 2500,
      outstanding: 42000,
      status: "Active",
      address: "Focal Point, Ludhiana"
    },
    {
      id: "JWK-005",
      name: "Modern Finishing",
      contactPerson: "Mahesh Joshi",
      mobile: "+91 98205 33445",
      process: "Finishing & Packing",
      rate: 10,
      rateUnit: "Piece",
      city: "Mumbai",
      capacityPerDay: 4000,
      outstanding: 60000,
      status: "Active",
      address: "Saki Naka, Andheri East"
    }
  ],

  // 4. Item Master
  items: [
    {
      id: "ITM-001",
      code: "FAB-COT-180",
      name: "100% Combed Cotton Fabric 180 GSM",
      type: "Raw Material",
      category: "Fabric",
      unit: "Meters",
      brand: "FashionWorks Raw",
      fabric: "100% Cotton",
      color: "Navy Blue / White / Black",
      size: "Free",
      hsn: "5208",
      rate: 145,
      openingStock: 10000,
      inwardStock: 12500,
      outwardStock: 8100,
      currentStock: 14400,
      reorderLevel: 2500,
      status: "Active"
    },
    {
      id: "ITM-002",
      code: "FAB-DEN-12OZ",
      name: "Heavy Indigo Denim Fabric 12 Oz",
      type: "Raw Material",
      category: "Fabric",
      unit: "Meters",
      brand: "FashionWorks Raw",
      fabric: "Cotton Spandex",
      color: "Dark Indigo",
      size: "Free",
      hsn: "5209",
      rate: 220,
      openingStock: 6000,
      inwardStock: 8000,
      outwardStock: 5200,
      currentStock: 8800,
      reorderLevel: 1500,
      status: "Active"
    },
    {
      id: "ITM-003",
      code: "ACC-BTN-18L",
      name: "Polyester Pearl Buttons 18L",
      type: "Accessories",
      category: "Trims",
      unit: "Gross (144 pcs)",
      brand: "TrimsPlus",
      fabric: "N/A",
      color: "White / Smoke",
      size: "18L",
      hsn: "9606",
      rate: 45,
      openingStock: 500,
      inwardStock: 800,
      outwardStock: 420,
      currentStock: 880,
      reorderLevel: 100,
      status: "Active"
    },
    {
      id: "ITM-004",
      code: "ACC-ZIP-YKK5",
      name: "YKK Brass Metal Zipper #5 (6 inch)",
      type: "Accessories",
      category: "Trims",
      unit: "Pieces",
      brand: "YKK",
      fabric: "Brass / Polyester Tape",
      color: "Antique Brass",
      size: "6 Inch",
      hsn: "9607",
      rate: 18,
      openingStock: 4000,
      inwardStock: 6000,
      outwardStock: 3500,
      currentStock: 6500,
      reorderLevel: 1000,
      status: "Active"
    },
    {
      id: "ITM-005",
      code: "GAR-TSH-001",
      name: "Premium Cotton Crew Neck T-Shirt",
      type: "Finished Goods",
      category: "T-Shirts",
      unit: "Pieces",
      brand: "Aarambh Signature",
      fabric: "100% Bio-Washed Cotton",
      color: "Black, Navy, White, Grey",
      size: "S, M, L, XL, XXL",
      hsn: "6109",
      rate: 350,
      openingStock: 3500,
      inwardStock: 8500,
      outwardStock: 4200,
      currentStock: 7800,
      reorderLevel: 1000,
      status: "Active"
    },
    {
      id: "ITM-006",
      code: "GAR-SHT-002",
      name: "Men's Slim Fit Formal Shirt",
      type: "Finished Goods",
      category: "Shirts",
      unit: "Pieces",
      brand: "Aarambh Executive",
      fabric: "Giza Cotton 60s",
      color: "Sky Blue, White, Charcoal",
      size: "38, 40, 42, 44",
      hsn: "6205",
      rate: 650,
      openingStock: 2200,
      inwardStock: 5400,
      outwardStock: 3100,
      currentStock: 4500,
      reorderLevel: 800,
      status: "Active"
    },
    {
      id: "ITM-007",
      code: "GAR-JNS-003",
      name: "Classic Regular Stretch Denim Jeans",
      type: "Finished Goods",
      category: "Jeans",
      unit: "Pieces",
      brand: "Aarambh Denim Co",
      fabric: "12 Oz Stretch Denim",
      color: "Dark Blue, Vintage Wash",
      size: "30, 32, 34, 36, 38",
      hsn: "6203",
      rate: 850,
      openingStock: 1800,
      inwardStock: 4200,
      outwardStock: 2500,
      currentStock: 3500,
      reorderLevel: 500,
      status: "Active"
    },
    {
      id: "ITM-008",
      code: "PKG-BOX-CORR",
      name: "3-Ply Branded Outer Corrugated Box",
      type: "Packaging",
      category: "Packaging",
      unit: "Pieces",
      brand: "FashionWorks Pack",
      fabric: "Kraft Paper",
      color: "Brown",
      size: "24x18x12 Inch",
      hsn: "4819",
      rate: 32,
      openingStock: 1200,
      inwardStock: 3000,
      outwardStock: 1500,
      currentStock: 2700,
      reorderLevel: 400,
      status: "Active"
    }
  ],

  // 5. Sizes and Colors Master
  sizes: ["XS", "S", "M", "L", "XL", "XXL", "XXXL"],
  colors: [
    { name: "Black", hex: "#0f172a" },
    { name: "White", hex: "#f8fafc" },
    { name: "Navy Blue", hex: "#1e3a8a" },
    { name: "Red", hex: "#dc2626" },
    { name: "Green", hex: "#16a34a" },
    { name: "Yellow", hex: "#ca8a04" },
    { name: "Grey", hex: "#64748b" }
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

  // 6. Customer Sales Orders (SO)
  salesOrders: [
    {
      id: "SO-2026-1045",
      pchNo: "0008",
      bno: "BNO-2026-881",
      customer: "ABC Fashion",
      customerId: "CUST-001",
      orderDate: "2026-08-13",
      deliveryDate: "2026-08-28",
      product: "100% Combed Cotton Fabric 180 GSM",
      itemCode: "FAB-COT-180",
      freight: "To Pay",
      remark: "Urgent seasonal batch. Pre-shrunk fabric required.",
      quantity: 5000,
      rate: 145,
      amount: 725000,
      priority: "High",
      status: "In Production",
      lotNo: "dh/0000/0008/01",
      stage: "Grey",
      items: [
        {
          lotNo: "dh/0000/0008/01",
          item: "100% Combed Cotton Fabric 180 GSM",
          stage: "Grey",
          qty: 3000,
          rate: 145,
          amount: 435000,
          transport: "VRL Logistics",
          lrNo: "LR-99214-B",
          netMeter: 3000
        },
        {
          lotNo: "dh/0000/0008/02",
          item: "Heavy Indigo Denim Fabric 12 Oz",
          stage: "Bleached",
          qty: 2000,
          rate: 220,
          amount: 440000,
          transport: "VRL Logistics",
          lrNo: "LR-99214-B",
          netMeter: 2000
        }
      ]
    },
    {
      id: "SO-2026-1046",
      pchNo: "0007",
      bno: "BNO-2026-790",
      customer: "Urban Wear",
      customerId: "CUST-002",
      orderDate: "2026-08-11",
      deliveryDate: "2026-09-02",
      product: "Classic Regular Stretch Denim Jeans",
      itemCode: "GAR-JNS-003",
      freight: "Paid",
      remark: "Heavy wash denim, double stitched pockets.",
      quantity: 3000,
      rate: 850,
      amount: 2550000,
      priority: "Urgent",
      status: "Material Planned",
      lotNo: "dh/0000/0007/01",
      stage: "Cutting",
      items: [
        {
          lotNo: "dh/0000/0007/01",
          item: "Classic Regular Stretch Denim Jeans",
          stage: "Cutting",
          qty: 3000,
          rate: 850,
          amount: 2550000,
          transport: "TCI Express",
          lrNo: "LR-88210-T",
          netMeter: 3000
        }
      ]
    },
    {
      id: "SO-2026-1047",
      pchNo: "0006",
      bno: "BNO-2026-745",
      customer: "Reliance Garments",
      customerId: "CUST-004",
      orderDate: "2026-08-08",
      deliveryDate: "2026-08-20",
      product: "Men's Slim Fit Formal Shirt",
      itemCode: "GAR-SHT-002",
      freight: "To Be Billed",
      remark: "Collar stiffness standard AQL 1.5.",
      quantity: 4500,
      rate: 650,
      amount: 2925000,
      priority: "Normal",
      status: "Ready for Dispatch",
      lotNo: "dh/0000/0006/01",
      stage: "Ready for Dispatch",
      items: [
        {
          lotNo: "dh/0000/0006/01",
          item: "Men's Slim Fit Formal Shirt",
          stage: "Ready for Dispatch",
          qty: 4500,
          rate: 650,
          amount: 2925000,
          transport: "Shree Balaji Roadlines",
          lrNo: "LR-77190-M",
          netMeter: 4500
        }
      ]
    },
    {
      id: "SO-2026-1048",
      pchNo: "0005",
      bno: "BNO-2026-620",
      customer: "StyleHub Retail",
      customerId: "CUST-003",
      orderDate: "2026-08-05",
      deliveryDate: "2026-08-16",
      product: "Premium Cotton Crew Neck T-Shirt",
      itemCode: "GAR-TSH-001",
      freight: "Paid",
      remark: "Bio-washed black crew neck tees.",
      quantity: 4200,
      rate: 350,
      amount: 1470000,
      priority: "Normal",
      status: "Dispatched",
      lotNo: "dh/0000/0005/01",
      stage: "Ready for Dispatch",
      items: [
        {
          lotNo: "dh/0000/0005/01",
          item: "Premium Cotton Crew Neck T-Shirt",
          stage: "Ready for Dispatch",
          qty: 4200,
          rate: 350,
          amount: 1470000,
          transport: "V-Trans Logistics",
          lrNo: "LR-VT-982145",
          netMeter: 4200
        }
      ]
    }
  ],

  // 9. Job Work Assignments
  jobWorks: [
    {
      id: "JW-2026-0008",
      assignNo: "0008",
      date: "2026-08-13",
      orderNo: "SO-2026-1045",
      customer: "ABC Fashion",
      item: "100% Combed Cotton Fabric 180 GSM",
      process: "Stitching",
      jobWorker: "Raj Stitching",
      jobWorkerId: "JWK-001",
      freight: "To Pay",
      factoryChallan: "FC-2026-8819",
      lrNo: "LR-99214-B",
      transport: "VRL Logistics",
      remark: "Urgent delivery batch for Autumn drop. Inspect neck rib tension before stitching.",
      lotNo: "LOT-2026-00145",
      stage: "Stitching Stage",
      meter: 5000,
      netMeter: 5000,
      quantity: 5000,
      rate: 25,
      totalAmount: 125000,
      outwardDate: "2026-08-13",
      expectedReturnDate: "2026-08-20",
      status: "In Progress",
      sentQty: 5000,
      receivedGoodQty: 4850,
      rejectedQty: 50,
      damagedQty: 20,
      pendingQty: 80,
      qcStatus: "Pending QC",
      items: [
        {
          item: "100% Combed Cotton Fabric 180 GSM",
          lotNo: "LOT-2026-00145",
          stage: "Stitching Stage",
          meter: 5000,
          netMeter: 5000,
          process: "Stitching",
          lrNo: "LR-99214-B",
          transport: "VRL Logistics",
          rate: 25,
          amount: 125000
        }
      ]
    },
    {
      id: "JW-2026-0007",
      assignNo: "0007",
      date: "2026-08-11",
      orderNo: "SO-2026-1045",
      customer: "ABC Fashion",
      item: "Premium Cotton Crew Neck T-Shirt",
      process: "Printing",
      jobWorker: "ColorPrint Works",
      jobWorkerId: "JWK-003",
      freight: "Paid",
      factoryChallan: "FC-2026-8802",
      lrNo: "LR-88210-T",
      transport: "TCI Express",
      remark: "Chest screen print - 3 color plastisol ink.",
      lotNo: "LOT-2026-00145",
      stage: "Printing Stage",
      meter: 4850,
      netMeter: 4850,
      quantity: 4850,
      rate: 15,
      totalAmount: 72750,
      outwardDate: "2026-08-11",
      expectedReturnDate: "2026-08-18",
      status: "Assigned",
      sentQty: 4850,
      receivedGoodQty: 0,
      rejectedQty: 0,
      damagedQty: 0,
      pendingQty: 4850,
      qcStatus: "Awaiting Inward",
      items: [
        {
          item: "Premium Cotton Crew Neck T-Shirt",
          lotNo: "LOT-2026-00145",
          stage: "Printing Stage",
          meter: 4850,
          netMeter: 4850,
          process: "Printing",
          lrNo: "LR-88210-T",
          transport: "TCI Express",
          rate: 15,
          amount: 72750
        }
      ]
    },
    {
      id: "JW-2026-0006",
      assignNo: "0006",
      date: "2026-08-08",
      orderNo: "SO-2026-1047",
      customer: "Reliance Garments",
      item: "Men's Slim Fit Formal Shirt",
      process: "Finishing & Packing",
      jobWorker: "Modern Finishing",
      jobWorkerId: "JWK-005",
      freight: "To Be Billed",
      factoryChallan: "FC-2026-8740",
      lrNo: "LR-77190-M",
      transport: "Shree Balaji Logistics",
      remark: "Steam ironing, tag attaching, and individual polybag packaging.",
      lotNo: "LOT-2026-00140",
      stage: "Finishing Stage",
      meter: 4600,
      netMeter: 4600,
      quantity: 4600,
      rate: 10,
      totalAmount: 46000,
      outwardDate: "2026-08-08",
      expectedReturnDate: "2026-08-14",
      status: "Completed",
      sentQty: 4600,
      receivedGoodQty: 4550,
      rejectedQty: 30,
      damagedQty: 20,
      pendingQty: 0,
      qcStatus: "Passed QC",
      items: [
        {
          item: "Men's Slim Fit Formal Shirt",
          lotNo: "LOT-2026-00140",
          stage: "Finishing Stage",
          meter: 4600,
          netMeter: 4600,
          process: "Finishing & Packing",
          lrNo: "LR-77190-M",
          transport: "Shree Balaji Logistics",
          rate: 10,
          amount: 46000
        }
      ]
    }
  ],

  // 10. Quality Check (QC) Records
  qualityChecks: [
    {
      id: "QC-2026-0312",
      jobWorkNo: "JW-2026-0024",
      lotNo: "LOT-2026-00145",
      item: "Premium Cotton Crew Neck T-Shirt",
      receivedQty: 5000,
      qcChecked: 4900,
      passedQty: 4700,
      reworkQty: 150,
      rejectedQty: 50,
      qcPerson: "Sunil Shinde",
      qcDate: "2026-08-15",
      remarks: "50 pcs neck rib loose, 150 sent back for minor thread trimming and iron touchup.",
      status: "Partially Passed"
    },
    {
      id: "QC-2026-0310",
      jobWorkNo: "JW-2026-0022",
      lotNo: "LOT-2026-00140",
      item: "Men's Slim Fit Formal Shirt",
      receivedQty: 4600,
      qcChecked: 4600,
      passedQty: 4550,
      reworkQty: 0,
      rejectedQty: 50,
      qcPerson: "Sunil Shinde",
      qcDate: "2026-08-17",
      remarks: "All 4550 pcs passed AQL 1.5 standard inspection. Fit and collar stiffness verified.",
      status: "Passed"
    }
  ],

  // 11. Production Lot Tracking Master
  lots: [
    {
      lotNo: "LOT-2026-00145",
      orderNo: "SO-2026-1045",
      customer: "ABC Fashion",
      product: "Premium Cotton Crew Neck T-Shirt",
      targetQty: 5000,
      currentQty: 4850,
      currentProcess: "Stitching",
      status: "In Progress",
      qrCodeString: "GARMENT-LOT-2026-000145",
      timeline: [
        { date: "12 Aug 2026", time: "10:30 AM", stage: "Order Created", qty: 5000, operator: "Admin User", note: "Customer PO #ABC-PO-982 confirmed" },
        { date: "13 Aug 2026", time: "02:15 PM", stage: "Material Available", qty: 5000, operator: "Store Mgr", note: "1,250 kg fabric issued from WH-01" },
        { date: "14 Aug 2026", time: "05:45 PM", stage: "Cutting Completed", qty: 5000, operator: "Cutting Dept", note: "Pattern cut on Gerber CNC system" },
        { date: "15 Aug 2026", time: "11:00 AM", stage: "Sent to Raj Stitching", qty: 5000, operator: "Dispatch Yard", note: "Outward Challan #JW-2026-0024" },
        { date: "17 Aug 2026", time: "04:30 PM", stage: "Received from Job Worker", qty: 4850, operator: "Inward Gate", note: "4,850 good pcs, 50 reject, 20 damage" },
        { date: "18 Aug 2026", time: "11:20 AM", stage: "Quality Check", qty: 4700, operator: "Sunil Shinde", note: "QC report #QC-2026-0312 created" }
      ]
    },
    {
      lotNo: "LOT-2026-00140",
      orderNo: "SO-2026-1047",
      customer: "Reliance Garments",
      product: "Men's Slim Fit Formal Shirt",
      targetQty: 4500,
      currentQty: 4550,
      currentProcess: "Packing Completed",
      status: "Ready for Dispatch",
      qrCodeString: "GARMENT-LOT-2026-000140",
      timeline: [
        { date: "08 Aug 2026", time: "09:00 AM", stage: "Order Created", qty: 4500, operator: "Admin User", note: "Sales order booked" },
        { date: "09 Aug 2026", time: "11:30 AM", stage: "Cutting Done", qty: 4600, operator: "Cutting Dept", note: "Fabric lay cut" },
        { date: "12 Aug 2026", time: "04:00 PM", stage: "Stitching & Collar Attach", qty: 4580, operator: "Raj Stitching", note: "Stitching completed" },
        { date: "15 Aug 2026", time: "02:00 PM", stage: "Finishing & Buttoning", qty: 4550, operator: "Modern Finishing", note: "Button attachment done" },
        { date: "17 Aug 2026", time: "05:00 PM", stage: "QC Passed & Boxed", qty: 4550, operator: "QC Team", note: "Ready in Warehouse Unit 2" }
      ]
    }
  ],

  // 12. Dispatch Management
  dispatches: [
    {
      id: "DO-2026-0512",
      orderDispatchNo: "0004",
      billNo: "BILL-2026-908",
      orderNo: "SO-2026-1048",
      customer: "StyleHub Retail",
      customerId: "CUST-003",
      invoiceNo: "INV-2026-0922",
      dispatchDate: "2026-08-13",
      transport: "V-Trans Logistics India",
      status: "In Transit",
      quantity: 4200,
      invoiceAmount: 698208,
      items: [
        {
          baleNo: "BALE-01",
          lotNo: "dh/0000/0005/01",
          item: "100% Combed Cotton Fabric 180 GSM",
          availableMeter: 5000,
          meter: 2400,
          netMeter: 2400,
          rate: 145,
          amount: 348000,
          gst: 62640,
          totalAmount: 410640
        },
        {
          baleNo: "BALE-02",
          lotNo: "dh/0000/0005/02",
          item: "Heavy Indigo Denim Fabric 12 Oz",
          availableMeter: 3000,
          meter: 1800,
          netMeter: 1800,
          rate: 135,
          amount: 243000,
          gst: 43740,
          totalAmount: 286740
        }
      ]
    },
    {
      id: "DO-2026-0511",
      orderDispatchNo: "0003",
      billNo: "BILL-2026-880",
      orderNo: "SO-2026-1045",
      customer: "ABC Fashion",
      customerId: "CUST-001",
      invoiceNo: "INV-2026-0890",
      dispatchDate: "2026-08-10",
      transport: "Safexpress Cargo",
      status: "Pending",
      quantity: 3000,
      invoiceAmount: 513300,
      items: [
        {
          baleNo: "BALE-01",
          lotNo: "dh/0000/0008/01",
          item: "100% Combed Cotton Fabric 180 GSM",
          availableMeter: 5000,
          meter: 3000,
          netMeter: 3000,
          rate: 145,
          amount: 435000,
          gst: 78300,
          totalAmount: 513300
        }
      ]
    }
  ],


  // 13. Customer Invoices & Accounts Settlements
  invoices: [
    {
      invoiceNo: "INV-2026-0922",
      orderNo: "SO-2026-1048",
      dispatchRef: "0004",
      customer: "StyleHub Retail",
      customerId: "CUST-003",
      customerGstin: "24AAACS9912E1Z8",
      customerAddress: "SG Highway, Prahlad Nagar, Ahmedabad, Gujarat - 380015",
      date: "2026-08-13",
      dueDate: "2026-08-28",
      paymentTerms: "15 Days Net",
      placeOfSupply: "Gujarat (24)",
      amount: 698208,
      paidAmount: 418208,
      balanceAmount: 280000,
      status: "Partially Paid",
      notes: "Goods dispatched via V-Trans Logistics under Challan 0004.",
      items: [
        {
          item: "100% Combed Cotton Fabric 180 GSM",
          hsn: "5208",
          lotNo: "LOT-2026-00145",
          baleNo: "BALE-01",
          qty: 2400,
          unit: "Meters",
          rate: 145,
          discount: 0,
          taxableAmount: 348000,
          gstRate: 18,
          gstAmount: 62640,
          totalAmount: 410640
        },
        {
          item: "Heavy Indigo Denim Fabric 12 Oz",
          hsn: "5209",
          lotNo: "LOT-2026-00140",
          baleNo: "BALE-02",
          qty: 1800,
          unit: "Meters",
          rate: 135,
          discount: 0,
          taxableAmount: 243000,
          gstRate: 18,
          gstAmount: 43740,
          totalAmount: 286740
        }
      ]
    },
    {
      invoiceNo: "INV-2026-0890",
      orderNo: "SO-2026-1032",
      dispatchRef: "0003",
      customer: "ABC Fashion",
      customerId: "CUST-001",
      customerGstin: "27AABCF1234F1Z5",
      customerAddress: "Phoenix Mills Compound, Lower Parel, Mumbai, Maharashtra - 400013",
      date: "2026-07-20",
      dueDate: "2026-08-19",
      paymentTerms: "30 Days Net",
      placeOfSupply: "Maharashtra (27)",
      amount: 513300,
      paidAmount: 800000,
      balanceAmount: 0,
      status: "Paid",
      notes: "Full payment received via NEFT. Thank you for your business!",
      items: [
        {
          item: "100% Combed Cotton Fabric 180 GSM",
          hsn: "5208",
          lotNo: "dh/0000/0008/01",
          baleNo: "BALE-01",
          qty: 3000,
          unit: "Meters",
          rate: 145,
          discount: 0,
          taxableAmount: 435000,
          gstRate: 18,
          gstAmount: 78300,
          totalAmount: 513300
        }
      ]
    },
    {
      invoiceNo: "INV-2026-0875",
      orderNo: "SO-2026-1020",
      dispatchRef: "0002",
      customer: "Urban Wear",
      customerId: "CUST-002",
      customerGstin: "07AAACU8821L1ZK",
      customerAddress: "Connaught Place, Central Delhi, New Delhi - 110001",
      date: "2026-07-10",
      dueDate: "2026-08-24",
      paymentTerms: "45 Days Net",
      placeOfSupply: "Delhi (07)",
      amount: 620000,
      paidAmount: 0,
      balanceAmount: 620000,
      status: "Unpaid",
      notes: "Payment due on or before 24 Aug 2026.",
      items: [
        {
          item: "Organic Slub Jersey Fabric",
          hsn: "5208",
          lotNo: "LOT-2026-00138",
          baleNo: "BALE-01",
          qty: 4000,
          unit: "Meters",
          rate: 130,
          discount: 0,
          taxableAmount: 520000,
          gstRate: 18,
          gstAmount: 93600,
          totalAmount: 613600
        }
      ]
    }
  ],

  // 14. Payment Receipts
  payments: [
    {
      id: "REC-2026-0341",
      customer: "ABC Fashion",
      invoiceNo: "INV-2026-0890",
      date: "2026-08-12",
      amount: 75000,
      mode: "Bank Transfer (NEFT)",
      refNo: "HDFC982347101",
      remarks: "Part payment towards July invoice"
    },
    {
      id: "REC-2026-0340",
      customer: "Urban Wear",
      invoiceNo: "INV-2026-0875",
      date: "2026-08-10",
      amount: 200000,
      mode: "RTGS",
      refNo: "ICIC00293847",
      remarks: "Advance for August bulk lot"
    }
  ],

  // 15. Item Ledger Transactions
  itemLedger: [
    { date: "2026-08-01", ref: "OB-2026-001", item: "100% Combed Cotton Fabric 180 GSM", type: "Opening Stock", inward: 10000, outward: 0, balance: 10000, user: "System", lot: "RAW-LOT-001" },
    { date: "2026-08-06", ref: "PO-2026-0812", item: "100% Combed Cotton Fabric 180 GSM", type: "Purchase Inward (GRN)", inward: 5000, outward: 0, balance: 15000, user: "Store Mgr", lot: "RAW-LOT-COT-8812" },
    { date: "2026-08-13", ref: "JW-2026-0024", item: "100% Combed Cotton Fabric 180 GSM", type: "Job Work Outward Issue", inward: 0, outward: 600, balance: 14400, user: "Sunil Shinde", lot: "LOT-2026-00145" },
    { date: "2026-08-01", ref: "OB-2026-002", item: "Premium Cotton Crew Neck T-Shirt", type: "Opening Stock", inward: 3500, outward: 0, balance: 3500, user: "System", lot: "FG-LOT-001" },
    { date: "2026-08-10", ref: "QC-2026-0308", item: "Premium Cotton Crew Neck T-Shirt", type: "Production Inward (QC Passed)", inward: 8500, outward: 0, balance: 12000, user: "QC Inspector", lot: "LOT-2026-00138" },
    { date: "2026-08-13", ref: "DO-2026-0512", item: "Premium Cotton Crew Neck T-Shirt", type: "Sales Dispatch", inward: 0, outward: 4200, balance: 7800, user: "Dispatch Mgr", lot: "LOT-2026-00138" }
  ],

  // 16. User & Permissions Management
  users: [
    { id: "USR-001", name: "Admin User", email: "admin@garmenterp.com", role: "Administrator", status: "Active", lastLogin: "Today 10:45 AM" },
    { id: "USR-002", name: "Sunil Shinde", email: "production@fashionworks.co.in", role: "Production Manager", status: "Active", lastLogin: "Today 09:30 AM" },
    { id: "USR-003", name: "Ramesh Thorat", email: "store@fashionworks.co.in", role: "Store Manager", status: "Active", lastLogin: "Yesterday 05:15 PM" },
    { id: "USR-004", name: "Kavita Rao", email: "accounts@fashionworks.co.in", role: "Accounts Manager", status: "Active", lastLogin: "Today 11:00 AM" },
    { id: "USR-005", name: "Mahesh Patil", email: "dispatch@fashionworks.co.in", role: "Dispatch Manager", status: "Active", lastLogin: "11 Aug 2026" }
  ],

  roles: [
    { role: "Administrator", permissions: { view: true, create: true, edit: true, delete: true, approve: true, export: true } },
    { role: "Production Manager", permissions: { view: true, create: true, edit: true, delete: false, approve: true, export: true } },
    { role: "Store Manager", permissions: { view: true, create: true, edit: true, delete: false, approve: false, export: true } },
    { role: "Accounts Manager", permissions: { view: true, create: true, edit: true, delete: false, approve: true, export: true } },
    { role: "Dispatch Manager", permissions: { view: true, create: true, edit: false, delete: false, approve: false, export: true } }
  ],

  // 17. Activity Audit Logs
  activityLogs: [
    { id: "LOG-001", user: "Admin User", role: "Administrator", action: "Created Sales Order", module: "Sales Orders", record: "SO-2026-1045", time: "10 mins ago", ip: "192.168.1.45" },
    { id: "LOG-002", user: "Production Manager", role: "Production", action: "Assigned Job Work", module: "Job Work", record: "JW-2026-0024 (Raj Stitching)", time: "25 mins ago", ip: "192.168.1.88" },
    { id: "LOG-003", user: "Store Manager", role: "Store", action: "Created Purchase Inward (GRN)", module: "Purchase", record: "GRN-2026-0410", time: "1 hour ago", ip: "192.168.1.62" },
    { id: "LOG-004", user: "QC Inspector", role: "Quality", action: "Logged QC Inspection (50 Rejections)", module: "QC", record: "QC-2026-0312", time: "2 hours ago", ip: "192.168.1.91" },
    { id: "LOG-005", user: "Dispatch Manager", role: "Dispatch", action: "Generated Dispatch Challan", module: "Dispatch", record: "DO-2026-0512 (4,200 pcs)", time: "4 hours ago", ip: "192.168.1.70" },
    { id: "LOG-006", user: "Accounts Manager", role: "Accounts", action: "Received Customer Payment ₹75,000", module: "Settlement", record: "REC-2026-0341 (ABC Fashion)", time: "5 hours ago", ip: "192.168.1.33" }
  ],

  // 18. Notifications List
  notifications: [
    { id: "NOTIF-1", title: "Job Work Received", desc: "JW-2026-0024 (4,850 pcs) received from Raj Stitching", time: "15m ago", type: "info", unread: true },
    { id: "NOTIF-2", title: "Low Stock Alert", desc: "Polyester Pearl Buttons 18L is below reorder level (880 Gross left)", time: "1h ago", type: "warning", unread: true },
    { id: "NOTIF-3", title: "Order Ready for Dispatch", desc: "Order SO-2026-1047 (4,550 pcs) is boxed in WH-02", time: "3h ago", type: "success", unread: true },
    { id: "NOTIF-4", title: "Payment Received", desc: "₹75,000 received from ABC Fashion via NEFT", time: "5h ago", type: "success", unread: false }
  ],

  // 19. Standalone Discount QR Vouchers & Single-Use Redemptions
  discountCoupons: [
    {
      id: "CPN-001",
      code: "SAVE500-A92B",
      type: "fixed",
      amount: 500,
      title: "Special Customer Discount",
      minBill: 1000,
      validTill: "2026-09-30",
      color: "#0f172a",
      usageType: "single",
      status: "Active",
      createdAt: "2026-08-13",
      timesScanned: 0,
      redeemedByPhone: null,
      redeemedAt: null,
      claimId: null
    },
    {
      id: "CPN-002",
      code: "FESTIVAL200-X77",
      type: "fixed",
      amount: 200,
      title: "Festival Celebration Offer",
      minBill: 500,
      validTill: "2026-10-15",
      color: "#4f46e5",
      usageType: "single",
      status: "Redeemed / Expired",
      createdAt: "2026-08-13",
      timesScanned: 1,
      redeemedByPhone: "+91 98201 55432",
      redeemedAt: "2026-09-07 16:45:12",
      claimId: "CLM-2026-0391"
    },
    {
      id: "CPN-003",
      code: "MEGA1000-VIP",
      type: "fixed",
      amount: 1000,
      title: "VIP Loyalty Reward",
      minBill: 3000,
      validTill: "2026-12-31",
      color: "#059669",
      usageType: "single",
      status: "Active",
      createdAt: "2026-08-12",
      timesScanned: 0,
      redeemedByPhone: null,
      redeemedAt: null,
      claimId: null
    },
    {
      id: "CPN-004",
      code: "WELCOME100-NEW",
      type: "fixed",
      amount: 100,
      title: "Welcome First Order Discount",
      minBill: 0,
      validTill: "2026-11-30",
      color: "#7c3aed",
      usageType: "single",
      status: "Redeemed / Expired",
      createdAt: "2026-08-10",
      timesScanned: 1,
      redeemedByPhone: "+91 98112 55678",
      redeemedAt: "2026-09-08 11:20:04",
      claimId: "CLM-2026-0402"
    },
    {
      id: "CPN-005",
      code: "FLASH50-INSTANT",
      type: "fixed",
      amount: 50,
      title: "Instant Counter Offer",
      minBill: 200,
      validTill: "2026-08-31",
      color: "#e11d48",
      usageType: "single",
      status: "Expired",
      createdAt: "2026-08-01",
      timesScanned: 0,
      redeemedByPhone: null,
      redeemedAt: null,
      claimId: null
    }
  ],
  purchaseOrders: [
    {
      id: "PO-2026-0001",
      poNumber: "PO-2026-0001",
      vendorId: "VND-001",
      vendorName: "Sri Krishna Textiles Ltd.",
      poDate: "2026-09-01",
      deliveryDate: "2026-09-15",
      warehouse: "Main Raw Material Store - Unit 1",
      paymentTerms: "30 Days Credit",
      status: "Approved",
      notes: "Urgent shipment required for festive line.",
      subtotal: 125000,
      taxAmount: 6250,
      grandTotal: 131250,
      items: [
        {
          id: 1,
          itemId: "ITM-001",
          itemName: "Premium Cotton Single Jersey 180 GSM",
          color: "Navy Blue",
          orderedQty: 500,
          unit: "Meters",
          unitPrice: 250,
          totalPrice: 125000,
          receivedQty: 0
        }
      ]
    },
    {
      id: "PO-2026-0002",
      poNumber: "PO-2026-0002",
      vendorId: "VND-002",
      vendorName: "Apex Trims & Accessories",
      poDate: "2026-09-03",
      deliveryDate: "2026-09-10",
      warehouse: "Main Raw Material Store - Unit 1",
      paymentTerms: "15 Days Credit",
      status: "Received",
      notes: "All buttons and zippers passed inspection.",
      subtotal: 45000,
      taxAmount: 2250,
      grandTotal: 47250,
      items: [
        {
          id: 2,
          itemId: "ITM-002",
          itemName: "Metallic Snap Buttons 15mm",
          color: "Silver",
          orderedQty: 10000,
          unit: "PCS",
          unitPrice: 4.5,
          totalPrice: 45000,
          receivedQty: 10000
        }
      ]
    }
  ]
};


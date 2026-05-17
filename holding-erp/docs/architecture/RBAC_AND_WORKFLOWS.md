# RBAC and Workflow Architecture

## 1. Role matrix

| Role group | Roles | Default scope |
| --- | --- | --- |
| Global holding | Owner, Global Director, Global Finance, Global IT, Global Legal, Global HRD, Global Audit | holding |
| Regional holding | Regional Manager, Regional Finance, Regional Warehouse, Regional Operational | city |
| Operational | Brand Admin, Warehouse Staff, Purchasing, Distribution Admin, Delivery Coordinator, Driver, Sales Distribution, Cashier, Kitchen, Production Staff, Outlet Supervisor | brand / branch / warehouse |

## 2. Permission matrix skeleton

| Module | View | Create | Update | Approve | Void | Export |
| --- | --- | --- | --- | --- | --- | --- |
| Holding | global/regional | global | global | global | - | global |
| Inventory | scoped | scoped | scoped | approval by rule | - | scoped |
| Purchasing | scoped | scoped | scoped | manager/finance | - | scoped |
| Distribution | scoped | scoped | scoped | manager | - | scoped |
| Delivery | scoped | scoped | scoped | coordinator | - | scoped |
| Finance | scoped/global | scoped | scoped | finance | finance | scoped/global |
| Tax | scoped/global | - | scoped | finance | - | scoped/global |
| POS | scoped | cashier | cashier supervisor | supervisor | supervisor | scoped |
| HRD | role-based | hr/admin | hr/admin | manager | - | hr/global |
| Legal | role-based | legal | legal | legal/global | - | legal/global |

Permission codes follow:

```text
approval.inbox.view
notifications.view
audit.log.view
inventory.stock.view
inventory.adjustment.create
inventory.adjustment.approve
purchasing.supplier.view
purchasing.supplier.create
purchasing.supplier.update
purchasing.supplier.deactivate
purchasing.purchase.create
purchasing.purchase.view
purchasing.purchase.approve
purchasing.purchase.receive
pos.sale.void
finance.payment.approve
tax.report.export
```

## 3. Inventory flow

```mermaid
flowchart LR
    A["Business Event"] --> B["Validated Command"]
    B --> C["Stock Movement"]
    C --> D["Warehouse Stock Projection"]
    D --> E["Low Stock / Expiry Checks"]
    E --> F["Notifications / Analytics"]
```

## 4. Purchasing flow

```mermaid
flowchart LR
    A["Supplier"] --> B["Purchase Order"]
    B --> C["Approval"]
    C --> D["Receiving"]
    D --> E["Stock Movement: purchase"]
    D --> F["Supplier Invoice"]
    F --> G["Payable / Payment"]
```

## 5. Production flow

```mermaid
flowchart LR
    A["Recipe / BOM"] --> B["Production Batch"]
    B --> C["Consume Raw Material"]
    C --> D["Create Finished Goods"]
    D --> E["Waste / Variance"]
    D --> F["Ready for POS / Distribution"]
```

## 6. Distribution and delivery flow

```mermaid
flowchart LR
    A["Sales Order / Internal Request"] --> B["Reservation"]
    B --> C["Pick & Pack"]
    C --> D["Delivery Order"]
    D --> E["Driver Assignment"]
    E --> F["Proof of Delivery"]
    F --> G["Receivable / Completion"]
```

## 7. Finance and tax flow

```mermaid
flowchart LR
    A["Commercial Event"] --> B["Invoice / Tax Calculation"]
    B --> C["Receivable or Payable"]
    C --> D["Payment"]
    D --> E["Cashflow"]
    E --> F["Financial Reports"]
    B --> G["Tax Reports"]
```

## 8. Brand-specific flows

### VINZ

```mermaid
flowchart LR
    A["Customer Order"] --> B["POS Payment"]
    B --> C["Queue Number"]
    C --> D["Kitchen Display"]
    D --> E["Kitchen Speaker"]
    E --> F["Prepared"]
    F --> G["Pickup Speaker"]
    G --> H["Customer Pickup"]
```

### SATE MERAH

```mermaid
flowchart LR
    A["ICONMART Raw Material"] --> B["Production Conversion"]
    B --> C["Finished Goods"]
    C --> D["Fast POS Sale"]
    D --> E["Finished Stock Reduction"]
```

### SHALIMAR

```mermaid
flowchart LR
    A["Website Booking"] --> B["Event Order"]
    B --> C["DP Payment"]
    C --> D["Production Planning"]
    D --> E["ICONMART Supply"]
    E --> F["Production"]
    F --> G["Delivery"]
    G --> H["Pelunasan"]
```

## 9. Notification architecture

| Trigger | Recipient |
| --- | --- |
| Low stock | warehouse, regional warehouse, purchasing |
| Pending approval | approvers by permission |
| Delivery event | coordinator, branch, customer-facing role |
| Finance due date | finance |
| Tax deadline | tax / finance |
| Contract expiry | legal / HRD |
| Attendance anomaly | HRD / supervisor |

Notifications are fan-out jobs, not synchronous controller work.





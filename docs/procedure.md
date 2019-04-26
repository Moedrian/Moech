# Procedure

## Vendor

With the information received from customers, vendor creates corresponding databases and tables.

### 1. Customers' signing up info

`Vendor::addCustomer` uses a private method `Vendor::vendorSimpleAdd` to insert customers' info into table `vendor.customers` then uses `Vendor::initCustomerDB` to create a customer database `moni_cust_id`.

### 2. Device list that customers want to monitoring

Create a `Vendor` instance then use `Vendor::addDevice` to add device information.

### 3. Orders that comes with devices' registration

`Vendor::addOrder` adds record to `vendor.orders` and `vendor.order_items`, then based on the boolean value `vendor.order_items.table.status`, `Vendor::addOrder` uses `Vendor::initCustomerDevice` to create tables in database `moni_cust_id`.

## Customer
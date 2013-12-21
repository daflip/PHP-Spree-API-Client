
# Simple High Performance Spree API Client for PHP

Requires curl php extension
 License: MIT

# Examples

## BEFORE YOU BEGIN! Set the spree URL and admin user token in the SpreeAPI class

``` php
// INIT YOUR API CLIENT:
$api = new SpreeAPI;
```

### Taxonomies
``` php
// get all taxonomies:
print_r($api->taxonomies());

// get specific taxon:
print_r($api->taxonomies(2));
```

### Orders
``` php
// get all orders
foreach ($api->orders()->orders as $order)
  print_r($order->order);

// get details on specific order:
print_r($api->orders('R285028844'));
```

### Products
``` php
//fetch all products
foreach ($api->products()->products as $product)
  print_r($product->product->name);

//only show products in the taxon category id 2:
print_r($api->products(array("q"=>array("taxons_id_eq" => 2))));

//only show products in the taxon category called 'QTK'
$api->products(array("q" => array("taxons_name_eq" => "QTK")));

```

### Error handling
Any request which fails will return false, you can obtain an error message by calling getError on the api class.

``` php
// fetch non existent order
if ($order = $api->orders('R911')) {
  print_r($order);
} else {
  print "Request Failed: ".$api->getError();
}
```

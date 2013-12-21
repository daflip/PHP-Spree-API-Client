
# Simple High Performance Spree API Client for PHP
 License: MIT

# EXAMPLES


``` php
// INIT YOUR API CLIENT:
$api = new SpreeAPI;
```

### TAXONOMIES:
``` php
// get all taxonomies:
print_r($api->taxonomies());

// get specific taxon:
print_r($api->taxonomies(2));
```

### ORDERS:
``` php
// get all orders
foreach ($api->orders()->orders as $order)
  print_r($order->order);

// get details on specific order:
print_r($api->orders('R285028844'));
```

### PRODUCTS:
``` php
//fetch all products
foreach ($api->products()->products as $product)
  print_r($product->product->name);

//only show products in the taxon category id 2:
print_r($api->products(array("q"=>array("taxons_id_eq" => 2))));

//only show products in the taxon category called 'QTK'
$api->products(array("q" => array("taxons_name_eq" => "QTK")));

```



 Simple High Performance Spree API Client for PHP

 License: MIT
```

   _______  __    _    __  __ ____  _     _____ ____  
  | ____\ \/ /   / \  |  \/  |  _ \| |   | ____/ ___| 
  |  _|  \  /   / _ \ | |\/| | |_) | |   |  _| \___ \ 
  | |___ /  \  / ___ \| |  | |  __/| |___| |___ ___) |
  |_____/_/\_\/_/   \_\_|  |_|_|   |_____|_____|____/ 

## INIT YOUR API CLIENT:
$api = new SpreeAPI;

## TAXONOMIES:
 get all taxonomies:
print_r($api->taxonomies());

 get specific taxon:
print_r($api->taxonomies(2));

## ORDERS:

 get all orders
foreach ($api->orders()->orders as $order)
  print_r($order->order);
 get details on specific order:
print_r($api->orders('R285028844'));

## PRODUCTS:
 fetch all products
foreach ($api->products()->products as $product)
  print_r($product->product->name);
 only show products in the taxon category id 2:
print_r($api->products(array("q"=>array("taxons_id_eq" => 2))));
 only show products in the taxon category called 'QTK'
$api->products(array("q" => array("taxons_name_eq" => "QTK")));
```

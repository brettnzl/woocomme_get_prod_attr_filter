<?php 

function get_products_ids_from_query_by_id( ) {

    $queried_object = get_queried_object();

    $products_IDs = new WP_Query( array(
        'post_type' => 'product',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'fields' => 'ids',
        'tax_query' => array(
            array(
                'taxonomy' => $queried_object->taxonomy,
                'field' => 'term_id',
                'terms' => $queried_object->term_id,
                'operator' => 'IN',
            )
        )
    ) );
    
    return $products_IDs;
}

function get_attributes_attached_to_IDs($products) 
{
    global $wpdb;

    $productidlist = implode(",",$products);
    // Select Product Attributes from meta 
    $attibuteMeta = $wpdb->get_results("SELECT meta_value FROM $wpdb->postmeta WHERE post_id IN ($productidlist) AND (meta_key = '_product_attributes' AND meta_value != 'a:0:{}')");

    // Now we have an array of the meta attributes.
    // We can tidy this into a group of attributes
    $attGroupArray = array();

    foreach ($attibuteMeta as $key => $prodAttr) {
        
        // Loop through attribute values 
        foreach (unserialize($prodAttr->meta_value) as $group => $values) {
            
            // value is a string so we need to turn it into an array and loop
            // the keys will be the color, and the value is product count
            foreach (explode(" | ", $values['value']) as $fk => $attr) {
                if (isset($attGroupArray[$group][$attr])) {
                    // if colour is already added increase count
                    $attGroupArray[$group][$attr] = $attGroupArray[$group][$attr] + 1;
    
                } else {
                    // add new colour to group
                    $attGroupArray[$group][$attr] = 1;
                }
            }
            
        }
        
    }

    echo "<pre>";
    print_R($attGroupArray);
    echo "</pre>";
    
}

?>


<div class="filter-extreme bg-light p-3">
    
    <?php 
        // Get array of all queried product ID's    
        $ids = get_products_ids_from_query_by_id(); 

        //Select Attribute where product_id in queried product id array
        $attributes = get_attributes_attached_to_IDs($ids->posts); 
    
    ?>


<!-- Select Product Attributes where not = a:0:{} -->


</div>

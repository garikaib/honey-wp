<?php
// seed-shop.php

$categories = [
    'Raw Honey',
    'Infused Honey',
    'Pantry',
    'Gifts',
    'Wholesale',
    'Honeyscoops',
    'Peanut Butter'
];

$cat_ids = [];

foreach ($categories as $cat) {
    if (!term_exists($cat, 'product_category')) {
        $term = wp_insert_term($cat, 'product_category');
        if (!is_wp_error($term)) {
            $cat_ids[$cat] = $term['term_id'];
            echo "Created Category: $cat\n";
        }
    } else {
        $term = get_term_by('name', $cat, 'product_category');
        $cat_ids[$cat] = $term->term_id;
        echo "Category exists: $cat\n";
    }
}

$products = [
    [
        'title' => 'Rainforest Honey',
        'category' => 'Raw Honey',
        'price' => 5000, // 50.00
        'sku' => 'RFH-001',
        'desc' => 'Pure, organic honey harvested from the deep rainforests. Rich in antioxidants and flavor.',
        'availability' => 'in_stock'
    ],
    [
        'title' => 'Wildflower Honey',
        'category' => 'Raw Honey',
        'price' => 4500,
        'sku' => 'WFH-002',
        'desc' => 'A floral blend of nectar from various wildflowers. Light, sweet, and perfect for tea.',
        'availability' => 'in_stock'
    ],
    [
        'title' => 'Chilli Infused Honey',
        'category' => 'Infused Honey',
        'price' => 6000,
        'sku' => 'CIH-001',
        'desc' => 'Sweet honey with a spicy kick. Perfect for glazes or drizzling over pizza.',
        'availability' => 'in_stock'
    ],
    [
        'title' => 'Honey Roasted Peanut Butter',
        'category' => 'Pantry',
        'price' => 1500,
        'sku' => 'HRPB-001',
        'desc' => 'Creamy peanut butter blended with our signature honey. A breakfast staple.',
        'availability' => 'in_stock'
    ],
    [
        'title' => 'Bulk Raw Honey (20kg Bucket)',
        'category' => 'Wholesale',
        'price' => 150000, // 1500.00
        'sku' => 'WS-RH-020',
        'desc' => 'Wholesale raw honey for industrial and culinary use. High quality, unpasteurized.',
        'availability' => 'in_stock'
    ],
    [
        'title' => 'Signature HoneyScoop (Medium)',
        'category' => 'Honeyscoops',
        'price' => 1200,
        'sku' => 'HS-001',
        'desc' => 'Our custom-designed wooden honeyscoop, perfect for drizzling without the mess.',
        'availability' => 'in_stock'
    ],
    [
        'title' => 'Crunchy Peanut Butter (500g)',
        'category' => 'Peanut Butter',
        'price' => 550,
        'sku' => 'PB-C-500',
        'desc' => 'All-natural crunchy peanut butter. Pure peanuts, high energy.',
        'availability' => 'in_stock'
    ]
];

foreach ($products as $p) {
    if (get_page_by_title($p['title'], OBJECT, 'product')) {
        echo "Product exists: {$p['title']}\n";
        continue;
    }

    $post_id = wp_insert_post([
        'post_title' => $p['title'],
        'post_content' => $p['desc'],
        'post_status' => 'publish',
        'post_type' => 'product'
    ]);

    if (!is_wp_error($post_id)) {
        wp_set_object_terms($post_id, $cat_ids[$p['category']], 'product_category');
        update_post_meta($post_id, '_product_price', $p['price']);
        update_post_meta($post_id, '_product_sku', $p['sku']);
        update_post_meta($post_id, '_product_availability', $p['availability']);
        echo "Created Product: {$p['title']}\n";
    }
}

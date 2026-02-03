<?php
// seed-posts.php

// Note: When running with `wp eval-file`, WordPress is already loaded.

$posts_data = [
    [
        'title' => 'The Health Benefits of Raw Honey',
        'content' => '<!-- wp:paragraph --><p>Raw honey has been used as a folk remedy throughout history and has a variety of health benefits and medical uses. It is even used in some hospitals as a treatment for wounds. Many of these health benefits are specific to raw, or unpasteurized, honey.</p><!-- /wp:paragraph --><!-- wp:paragraph --><p>Most of the honey you find in grocery stores is pasteurized. The high heat kills unwanted yeast, can improve the color and texture, removes any crystallization, and extends the shelf life. Many of the beneficial nutrients are also destroyed in the process.</p><!-- /wp:paragraph -->',
        'category' => 'Honey Knowledge',
    ],
    [
        'title' => 'Why Our Bees Are So Happy',
        'content' => '<!-- wp:paragraph --><p>Our bees forage in the pristine wilderness, far from pesticides and pollutants. This not only ensures the purity of our honey but also the health and happiness of our hives.</p><!-- /wp:paragraph --><!-- wp:paragraph --><p>We believe in sustainable beekeeping practices that prioritize the well-being of the colony over mass production. Happy bees make better honey!</p><!-- /wp:paragraph -->',
        'category' => 'Bee Health',
    ],
    [
        'title' => 'New Product Alert: Lavender Infused Honey',
        'content' => '<!-- wp:paragraph --><p>We are excited to announce our newest addition to the family: Lavender Infused Honey. This delicate blend combines the floral notes of organic lavender with our signature wildflower honey.</p><!-- /wp:paragraph --><!-- wp:paragraph --><p>It is perfect for sweetening tea, drizzling over yogurt, or even as a glaze for roasted vegetables. Try it today and experience the taste of summer.</p><!-- /wp:paragraph -->',
        'category' => 'Product Updates',
    ],
    [
        'title' => '5 Ways to Use Honey in Your Skincare Routine',
        'content' => '<!-- wp:paragraph --><p>Honey isn’t just for eating! It’s also a powerful skincare ingredient. Thanks to its antibacterial and antiseptic abilities, it may benefit oily and acne-prone skin.</p><!-- /wp:paragraph --><!-- wp:list --><ul><li>Face Mask: Mix with cinnamon.</li><li>Exfoliator: Mix with sugar.</li><li>Scar Fader: Apply directly.</li></ul><!-- /wp:list -->',
        'category' => 'Honey Knowledge',
    ],
    [
        'title' => 'The Journey from Flower to Jar',
        'content' => '<!-- wp:paragraph --><p>Ever wondered how honey gets from the flower to your table? It starts with the nectar that bees collect. They store it in their extra stomach where it mixes with enzymes...</p><!-- /wp:paragraph -->',
        'category' => 'Honey Knowledge',
    ],
    [
        'title' => 'Saving the Bees: How You Can Help',
        'content' => '<!-- wp:paragraph --><p>Bee populations are declining globally, but there are things you can do to help. Planting a bee-friendly garden is one of the most impactful steps you can take.</p><!-- /wp:paragraph -->',
        'category' => 'Bee Health',
    ],
    [
        'title' => 'Recipe: Honey Glazed Salmon',
        'content' => '<!-- wp:paragraph --><p>This quick and easy honey glazed salmon recipe is a weeknight favorite. The sweet and savory glaze caramelizes perfectly in the oven.</p><!-- /wp:paragraph -->',
        'category' => 'Recipes',
    ],
    [
        'title' => 'Why Honey Crystallizes (And Why It’s Good)',
        'content' => '<!-- wp:paragraph --><p>Crystallization is a natural process and a sign of high-quality, raw honey. If your honey turns solid, don’t worry! It hasn’t gone bad.</p><!-- /wp:paragraph -->',
        'category' => 'Honey Knowledge',
    ],
    [
        'title' => 'Our Sustainable Packaging Initiative',
        'content' => '<!-- wp:paragraph --><p>We are committed to reducing our environmental footprint. That’s why we are switching to 100% recycled glass jars and biodegradable labels.</p><!-- /wp:paragraph -->',
        'category' => 'Product Updates',
    ],
    [
        'title' => 'Meet the Beekeepers',
        'content' => '<!-- wp:paragraph --><p>Get to know the team behind the honey. Our family has been keeping bees for three generations, passing down knowledge and passion.</p><!-- /wp:paragraph -->',
        'category' => 'Inside the Hive',
    ],
];

// Image IDs found from `ddev wp post list --post_type=attachment`
$image_ids = [26, 25, 22, 23, 24, 21, 20, 19, 18, 16];

foreach ( $posts_data as $data ) {
    // Check if post already exists
    $existing_post = get_page_by_title( $data['title'], OBJECT, 'post' );
    if ( $existing_post ) {
        echo "Post '{$data['title']}' already exists.\n";
        continue;
    }

    // Create Category
    $cat_id = wp_create_category( $data['category'] );

    // Create Post
    $post_id = wp_insert_post( [
        'post_title'   => $data['title'],
        'post_content' => $data['content'],
        'post_status'  => 'publish',
        'post_type'    => 'post',
        'post_category' => [ $cat_id ],
    ] );

    if ( $post_id ) {
        echo "Created post: {$data['title']} (ID: $post_id)\n";

        // Assign Random Image
        $random_image_id = $image_ids[ array_rand( $image_ids ) ];
        set_post_thumbnail( $post_id, $random_image_id );
        echo " - Assigned Featured Image ID: $random_image_id\n";
    } else {
        echo "Failed to create post: {$data['title']}\n";
    }
}

echo "Seeding complete.\n";

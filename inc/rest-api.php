<?php 
add_action( 'rest_api_init', function () {
  register_rest_route( 'university/v1', 'universities', array(
    'methods' => 'GET',
    'callback' => 'getResults',
  ) );
} );

function getResults($data) {
    // print_r($data);
    $university = new WP_Query(
        array( 
            'post_type' => ['post','page','professors','event','programmes'],
            's' => $data['term']
        )
    );
    $new_array = [
        'general_info' => [],
        'professors' => [],
        'programmes' => [],
        'events' => []
    ];
    while($university->have_posts()) {
        $university->the_post();
        // array_push($new_array, array( 
        //     "title" => get_the_title(),
        //     "permalink" => get_the_permalink()
        // ));
        if(get_post_type() == 'post' OR get_post_type() == 'page') {
            array_push($new_array['general_info'], array(
                "title" => get_the_title(),
                "permalink" => get_the_permalink(),
                "postType" => get_post_type(),
                "authorName" => get_author_name()
            ));
        }
        if(get_post_type() == 'professors') {
            array_push($new_array['professors'], array(
                "title" => get_the_title(),
                "permalink" => get_the_permalink(),
                "postType" => get_post_type(),
                "authorName" => get_author_name(),
                "image" => get_the_post_thumbnail_url(0, 'professorLandscape')
            ));
        }
        if(get_post_type() == 'programmes') {
            array_push($new_array['programmes'], array(
                "title" => get_the_title(),
                "permalink" => get_the_permalink(),
                "postType" => get_post_type(),
                "authorName" => get_author_name(),
                "ID" => get_the_ID()
            ));
        }
        if(get_post_type() == 'event') {
            $eventsDate = new DateTime(get_field('events_date'));
            $description = wp_trim_words( get_the_content(), 25 );
            array_push($new_array['events'], array(
                "title" => get_the_title(),
                "permalink" => get_the_permalink(),
                "postType" => get_post_type(),
                "authorName" => get_author_name(),
                "date" => $eventsDate->format('d'),
                "month" => $eventsDate->format('M'),
                "description" => $description
            ));
        }
    }

    //search relationship
    if(get_post_type() == 'programmes') {
        $listSubject = $new_array['programmes'];
        foreach($listSubject as $item) {
            $query = array(
                array (
                    'key' => 'related_programs',
                    'compare' => 'LIKE',
                    'value' => '"' . $item['ID'] . '"'
                )
            );
        }
        $relatedPrograms = new WP_Query(
            array(
                'post_type' => 'professors',
                'relation' => 'OR',
                'meta_query' => $query
            )
        );
    
        while($relatedPrograms->have_posts()) {
            $relatedPrograms->the_post();
            array_push($new_array['professors'], array(
                "title" => get_the_title(),
                "permalink" => get_the_permalink(),
                "postType" => get_post_type(),
                "authorName" => get_author_name(),
                "image" => get_the_post_thumbnail_url(0, 'professorLandscape')
            ));
        }
    }
    return  $new_array;
}
<?php
add_action('rest_api_init', 'universityRegisterSearch');
function universityRegisterSearch() {
  register_rest_route('ficUniverity/v1','search',array(
    'methods' => WP_REST_SERVER::READABLE,
    'callback' => 'universitySearchResults',
     'permission_callback' => '__return_true'
  ));
}

function universitySearchResults($data){
    $mainQuerySearch = new WP_Query(array(
        'post_type' => array('post','page','professor','program','event','campus'),
        's'=> sanitize_text_field($data['term'])
    ));

    $searchResults = array(
        'generalInfo' => array(),
        'professors' => array(),
        'programs' => array(),
        'events' => array(),
        'campuses' => array()
    ); 

    while($mainQuerySearch->have_posts()){
        $mainQuerySearch->the_post();
        if(get_post_type() ==='post'OR get_post_type() ==='page'){
            array_push($searchResults['generalInfo'], array(
                'title' => get_the_title(),
                'permalink' => get_the_permalink(),
                'postType' => get_post_type(),
                'authorName' => get_the_author()
            ));
        }  
        if(get_post_type() ==='professor'){
                    array_push($searchResults['professors'], array(
                        'title' => get_the_title(),
                        'permalink' => get_the_permalink(),
                        'image' => get_the_post_thumbnail_url(0,'professorLandscape')
                    ));
        }  
    
        if(get_post_type() ==='program'){
            $relatedCampuses = get_field('related_campus');
            if($relatedCampuses){
                    foreach($relatedCampuses as $campus){
                        array_push($searchResults['campuses'], array(
                            'title' => get_the_title($campus),
                            'permalink' => get_the_permalink($campus)
                        ));
                    }
                }

                    array_push($searchResults['programs'], array(
                        'title' => get_the_title(),
                        'permalink' => get_the_permalink(),
                        'id' => get_the_id()
                    ));
        }  
        if(get_post_type() ==='event'){
            $description = null;
            if(has_excerpt()){
                 $description = get_the_excerpt();
                   } else {
                            $description = wp_trim_words(get_the_content(), 18); // raw output (WP functions with markup)
                  }
                $eventDateValue = new DateTime(get_field('event_date'));
                    array_push($searchResults['events'], array(
                        'title' => get_the_title(),
                        'permalink' => get_the_permalink(),
                        'month' => $eventDateValue->format('M'),
                        'day' => $eventDateValue->format('d'),
                        'description' => $description,
                    ));
        }  
        if(get_post_type() ==='campus'){
                    array_push($searchResults['campuses'], array(
                        'title' => get_the_title(),
                        'permalink' => get_the_permalink(),
                    ));
        }  
   }

   if($searchResults['programs']){
        $programsMetaQuery = array('relation' => 'OR');
        foreach($searchResults['programs'] as $dataPrograms){
            array_push($programsMetaQuery, array(
                'key' => 'related_programs',
                'compare' => 'LIKE',
                'value' => '"' . $dataPrograms['id'] . '"'
            ));
        }

      $programRelationshipQuery = new WP_Query(array(
              'post_type' => array('professor','event'),
            'meta_query'=> $programsMetaQuery
        ));

while($programRelationshipQuery->have_posts()){
    $programRelationshipQuery->the_post();

    if(get_post_type() === 'event'){
        $eventDateValue = new DateTime(get_field('event_date'));
        $description = has_excerpt() ? get_the_excerpt() : wp_trim_words(get_the_content(), 18);

        array_push($searchResults['events'], array(
            'title' => get_the_title(),
            'permalink' => get_the_permalink(),
            'month' => $eventDateValue->format('M'),
            'day' => $eventDateValue->format('d'),
            'description' => $description,
        ));
    }

    if(get_post_type() === 'professor'){
        array_push($searchResults['professors'], array(
            'title' => get_the_title(),
            'permalink' => get_the_permalink(),
            'image' => get_the_post_thumbnail_url(0,'professorLandscape')
        ));
    }
}


        $searchResults['professors'] = array_values(array_unique($searchResults['professors'], SORT_REGULAR));
        $searchResults['events'] = array_values(array_unique($searchResults['events'], SORT_REGULAR));
   }
return $searchResults;
}
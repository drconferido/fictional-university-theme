 <div class="event-summary">
             <a class="event-summary__date t-center" href="#">
              <span class="event-summary__month"><?php 
               $eventDateValue = get_field('event_date');
                                if ($eventDateValue) {
                      // Gumawa ng DateTime object mula sa ACF value
                      $eventDate = new DateTime($eventDateValue);
                      // Hiwalay na format
                      $month = $eventDate->format('M'); // e.g. May
                      $day   = $eventDate->format('d'); // e.g. 06

                      echo $month;
                      ?></span>
                      <span class="event-summary__day"><?php echo $day; }
                                      else {
                            echo 'No event date set';
                        }
              ?></span>
            </a>
             <div class="event-summary__content">
              <h5 class="event-summary__title headline headline--tiny"><a href="<?php the_permalink();  ?>"> <?php the_title(); ?> </a></h5>
              <p><?php 
              
              if(has_excerpt()){
                echo get_the_excerpt();
              } else {
                echo wp_trim_words(get_the_content(), 18); // raw output (WP functions with markup)
              }?> <a href="<?php the_permalink();  ?>" class="nu gray">Learn more</a></p>
          </div>  
            </div>
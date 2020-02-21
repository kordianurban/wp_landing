<?php
/**
 * Projects listing
 * widget
 */

$projects = get_query_var('projects');

if ( $projects->have_posts() ): ?>

    <div class="widget projects-listing">

        <?php while ( $projects->have_posts() ): $projects->the_post();
            $url = Theme::$titan->getOption(Projects::META_URL); ?>

            <article>
                <div class="row">
                    <div class="col-lg-4 col-md-4 col-sm-12">
                        <?php the_post_thumbnail(); ?>
                    </div>

                    <div class="col-lg-4 col-md-4 col-sm-12">
                        <?php the_content(); ?>
                    </div>

                    <div class="col-lg-4 col-md-4 col-sm-12">
                        <h2><?php the_title(); ?></h2>
                        <?php the_excerpt(); ?>
                        <?php if ( strlen($url) > 10 ): ?>
                            <a class="btn" href="<?php echo $url; ?>" target="_blank">See the Project &rarr;</a>
                        <?php endif; ?>
                    </div>
                </div>
            </article>

        <?php endwhile; ?>

    </div>

<?php endif;
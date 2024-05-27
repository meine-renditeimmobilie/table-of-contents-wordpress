<?php get_header(); ?>
<?php if ( have_posts() ): while( have_posts() ): the_post(); ?>
	<div class="mri-blog-grid">
      <div class="mri-toc">
          <nav>
            <h2>Table of contents</h2>
            <ul><?php echo get_post_meta( $post->ID, "_mrionpagenav", true ); ?></ul>
          </nav>
      </div>

      <!-- The following content is the content of the given blog post and would typically be dynamically inserted by wordpress -->
      <article>
           ....
      
          <section>
            <h2>Section Title here</h2>
            ....
          </section>
          <section>
            <h2>Section Title here</h2>
            ....
          </section>
          <section>
            <h2>Section Title here</h2>
            ....
          </section>
          <section>
            <h2>Section Title here</h2>
            ....
          </section>

          ...
    </article>
    <!-- dynamic by Wordpress -->
    
</div>
<?php endwhile; endif; ?>
<?php get_footer(); ?>


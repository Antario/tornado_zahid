<?php
if( comments_open() ){
    if( is_user_logged_in() ){
        echo '<div id="comments" class="comments-list user-logged-in">';
    }
    else{
        echo '<div id="comments" class="comments-list">';
    }

    if ( post_password_required() ){
        echo '<p class="nopassword">';
        _e( 'This post is password protected. Enter the password to view any comments.' , 'materialize' );
        echo '</p>';
        echo '</div>';
        return;
    }

    /* IF EXISTS WORDPRESS COMMENTS */
    if ( have_comments() ) {
        $nr = absint( get_comments_number() );

        echo '<div class="mythemes-comments">';
        echo '<h3 class="comments-title">';
        echo sprintf( _nx( 'Відгук ( %s )' , 'Comments ( %s )' , $nr , 'Title before comment(s) list' , 'materialize' ) , '<strong>' . number_format_i18n( $nr ) . '</strong>' );
        echo '</h3>';
		
        echo '<ol>';

        wp_list_comments( array(
            'callback' => array( 'mythemes_comments' , 'classic' ),
            'style' => 'ul' 
        ));

        echo '</ol>';
        echo '</div>';

        $args = array(
            'echo'      => false,
            'prev_text' => sprintf( __( '%s Prev' , 'materialize' ) , '<i class="icon-left-open-1"></i>' ),
            'next_text' => sprintf( __( 'Next %s' , 'materialize' ) , '<i class="icon-right-open-1"></i>' )
        );

        $pgn = paginate_comments_links( $args );
        
        /* WORDPRESS PAGINATION FOR COMMENTS */
        if( !empty( $pgn ) ){
            echo '<div class="pagination aligncenter comments">';
            echo '<nav class="mythemes-nav-inline">';
            echo $pgn;
            echo '</nav>';
            echo '</div>';    
        }
        
    }
	
    /* FORM SUBMIT COMMENTS */
    $commenter = wp_get_current_commenter();

    /* CHECK VALUES */
    if( esc_attr( $commenter[ 'comment_author' ] ) )
        $name = esc_attr( $commenter[ 'comment_author' ] );
    else
        $name = __( 'Nickname ( required )' , 'materialize' );

    if( esc_attr( $commenter[ 'comment_author_email' ] ) )
        $email = esc_attr( $commenter[ 'comment_author_email' ] );
    else
        $email = __( 'E-mail ( required )' , 'materialize' );

    if( esc_attr( $commenter[ 'comment_author_url' ] ) )
        $web = esc_attr( $commenter[ 'comment_author_url' ] );
    else
        $web = __( 'Website' , 'materialize' );

    /* FIELDS */
    $fields =  array(
        'author' => '<div class="field">'.
                '<p class="comment-form-author input">'.
                '<input class="required span7" value="' . $name . '" onfocus="if (this.value == \'' . __( 'Nickname ( required )' , 'materialize' ). '\') {this.value = \'\';}" onblur="if (this.value == \'\' ) { this.value = \'' . __( 'Nickname ( required )' , 'materialize' ) . '\';}" id="author" name="author" type="text" size="30"  />' .
            '</p>',
        'email'  => '<p class="comment-form-email input">'.
                '<input class="required span7" value="' . $email . '" onfocus="if (this.value == \'' . __( 'E-mail ( required )' , 'materialize' ). '\') {this.value = \'\';}" onblur="if (this.value == \'\' ) { this.value = \'' . __( 'E-mail ( required )' , 'materialize' ) . '\';}" id="email" name="email" type="text" size="30" />' .
            '</p>',
        'url'    => '<p class="comment-form-url input">'.
                '<input class="span7" value="' . $web . '" onfocus="if (this.value == \'' . __( 'Website' , 'materialize' ). '\') {this.value = \'\';}" onblur="if (this.value == \'\' ) { this.value = \'' . __( 'Website' , 'materialize' ). '\';}" id="url" name="url" type="text" size="30" />' .
            '</p></div>',
    );
    

    $rett  = '<div class="textarea row-fluid"><p class="comment-form-comment textarea user-not-logged-in">';
    $rett .= '<label for="comment">' . __( 'Текст відгуку' , 'materialize' ) . '</label>';
    $rett .= '<textarea id="comment" name="comment" cols="45" rows="10" class="materialize-textarea" aria-required="true"></textarea>';
    $rett .= '</p></div>';

    global $wp_customize;

    if( isset( $wp_customize ) ) {
        $html = true;
        $classes = !(bool)get_theme_mod( 'mythemes-html-suggestions', true ) ? 'hide' : '';
    }
    else{
        $html = (bool)get_theme_mod( 'mythemes-html-suggestions', true );
        $classes = '';
    }

    if( $html ){
        $rett .= '<div class="mythemes-html-suggestions ' . esc_attr( $classes ) . '">';
        $rett .= '<p class="comment-notes">' . __( 'You may use these HTML tags and attributes' , 'materialize' ) . ':</p>';
        $rett .= '<pre>';
        $rett .= htmlspecialchars( '<a href="" title=""> <abbr title=""> <acronym title=""> <b> <blockquote cite=""> <cite> <code> <del datetime=""> <em> <i> <q cite=""> <strike> <strong>' );
        $rett .= '</pre>';
        $rett .= '</div>';
    }

    $args = array(	
        'title_reply' => __( "Залиште свій відгук" , 'materialize' ),
        'comment_notes_after'   => '',
        'comment_notes_before'  => '<button type="submit" class="btn waves-effect waves-light submit-comment">' . __( 'Відправити' , 'materialize' ) . '</button><p class="comment-notes">' . __( 'Your email address will not be published.' , 'materialize' ) . '</p>',
        'logged_in_as'          => '<button type="submit" class="btn waves-effect waves-light submit-comment">' . __( 'Відправити' , 'materialize' ) . '</button><p class="logged-in-as">' . __( 'Ви ввійшли як' , 'materialize' ) . ' <a href="' . esc_url( home_url( '/wp-admin/profile.php' ) ) . '">' . get_the_author_meta( 'nickname' , get_current_user_id() ) . '</a>. <a href="' . wp_logout_url( get_permalink( $post -> ID ) ) .'" title="' . __( 'Log out of this account' , 'materialize' ) . '">' . __( 'Вийти?' , 'materialize' ) . ' </a></p>',
        'fields'                => apply_filters( 'comment_form_default_fields', $fields ),
        'comment_field'         => $rett,
        'label_submit'          => __( 'Відправити' , 'materialize' )
    );

    echo '<div class="mythemes-comments">';
    comment_form( $args );
    echo '<div class="clearfix"></div>';
    echo '</div>';
    echo '</div>';
}
?>
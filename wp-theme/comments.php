<?php
/**
 * The template for displaying comments
 *
 * @package Asad_Portfolio_Manager
 */

if (post_password_required()) {
    return;
}
?>

<div id="comments" class="comments-area">

    <?php if (have_comments()) : ?>
        <h2 class="comments-title">
            <?php
            $comment_count = get_comments_number();
            if ('1' === $comment_count) {
                printf(
                    __('One comment on &ldquo;%s&rdquo;', 'asad-portfolio'),
                    '<span>' . get_the_title() . '</span>'
                );
            } else {
                printf(
                    _n(
                        '%1$s comment on &ldquo;%2$s&rdquo;',
                        '%1$s comments on &ldquo;%2$s&rdquo;',
                        $comment_count,
                        'asad-portfolio'
                    ),
                    number_format_i18n($comment_count),
                    '<span>' . get_the_title() . '</span>'
                );
            }
            ?>
        </h2><!-- .comments-title -->

        <ol class="comment-list">
            <?php
            wp_list_comments(array(
                'style'       => 'ol',
                'short_ping'  => true,
                'avatar_size' => 60,
                'callback'    => 'asad_custom_comment',
            ));
            ?>
        </ol><!-- .comment-list -->

        <?php
        the_comments_navigation();

        if (!comments_open()) :
            ?>
            <p class="no-comments"><?php _e('Comments are closed.', 'asad-portfolio'); ?></p>
        <?php endif; ?>

    <?php endif; // Check for have_comments(). ?>

    <?php
    comment_form(array(
        'title_reply_before' => '<h3 id="reply-title" class="comment-reply-title">',
        'title_reply_after'  => '</h3>',
        'class_submit'       => 'btn btn-primary',
        'submit_button'      => '<button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> %4$s</button>',
    ));
    ?>

</div><!-- #comments -->

<style>
.comments-area {
    margin-top: 3rem;
    padding: 2rem;
    background: var(--bg-color);
    border: 1px solid var(--border-color);
    border-radius: 8px;
}

.comments-title {
    font-size: 1.75rem;
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid var(--primary-color);
}

.comment-list {
    list-style: none;
    padding: 0;
}

.comment {
    margin-bottom: 2rem;
    padding: 1.5rem;
    background: var(--bg-color);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    position: relative;
}

.comment .children {
    list-style: none;
    margin-left: 2rem;
    margin-top: 1rem;
}

.comment-author {
    margin-bottom: 0.5rem;
}

.comment-author .avatar {
    border-radius: 50%;
    float: left;
    margin-right: 1rem;
}

.comment-author .fn {
    font-weight: bold;
    font-style: normal;
}

.comment-metadata {
    font-size: 0.875rem;
    color: var(--text-color);
    opacity: 0.7;
    margin-bottom: 1rem;
}

.comment-metadata a {
    color: inherit;
}

.comment-content {
    clear: both;
    padding-top: 1rem;
}

.comment-reply-link {
    display: inline-block;
    padding: 0.5rem 1rem;
    background: var(--secondary-color);
    color: #fff;
    border-radius: 4px;
    font-size: 0.875rem;
    margin-top: 0.5rem;
    transition: all 0.3s;
}

.comment-reply-link:hover {
    background: var(--primary-color);
}

.comment-awaiting-moderation {
    display: block;
    padding: 0.75rem 1rem;
    background: var(--accent-color);
    color: #fff;
    border-radius: 4px;
    margin-bottom: 1rem;
}

.comment-form {
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid var(--border-color);
}

.comment-form-comment,
.comment-form-author,
.comment-form-email,
.comment-form-url {
    margin-bottom: 1.5rem;
}

.comment-form label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
}

.comment-form input[type="text"],
.comment-form input[type="email"],
.comment-form input[type="url"],
.comment-form textarea {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid var(--border-color);
    border-radius: 4px;
    background: var(--bg-color);
    color: var(--text-color);
    font-family: var(--font-primary);
}

.comment-form input:focus,
.comment-form textarea:focus {
    outline: none;
    border-color: var(--primary-color);
}

.comment-form textarea {
    min-height: 150px;
    resize: vertical;
}

.comment-form .form-submit {
    margin: 0;
}

.no-comments {
    padding: 1rem;
    background: var(--border-color);
    text-align: center;
    border-radius: 4px;
    color: var(--text-color);
    opacity: 0.7;
}

.comment-navigation {
    margin: 2rem 0;
    display: flex;
    justify-content: space-between;
}

.comment-navigation a {
    padding: 0.5rem 1rem;
    background: var(--primary-color);
    color: #fff;
    border-radius: 4px;
}
</style>

<?php
/**
 * Custom comment callback
 */
function asad_custom_comment($comment, $args, $depth) {
    if ('div' === $args['style']) {
        $tag       = 'div';
        $add_below = 'comment';
    } else {
        $tag       = 'li';
        $add_below = 'div-comment';
    }
    ?>
    <<?php echo $tag; ?> <?php comment_class(empty($args['has_children']) ? '' : 'parent'); ?> id="comment-<?php comment_ID(); ?>">
    <?php if ('div' != $args['style']) : ?>
        <div id="div-comment-<?php comment_ID(); ?>" class="comment-body">
    <?php endif; ?>

    <div class="comment-author vcard">
        <?php if (0 != $args['avatar_size']) {
            echo get_avatar($comment, $args['avatar_size']);
        } ?>
        <?php
        printf(
            '<cite class="fn">%s</cite>',
            get_comment_author_link()
        );
        ?>
        <?php if ('0' == $comment->comment_approved) : ?>
            <span class="comment-awaiting-moderation"><?php _e('Your comment is awaiting moderation.', 'asad-portfolio'); ?></span>
        <?php endif; ?>
    </div>

    <div class="comment-metadata">
        <a href="<?php echo esc_url(get_comment_link($comment->comment_ID, $args)); ?>">
            <i class="far fa-clock"></i>
            <?php
            printf(
                __('%1$s at %2$s', 'asad-portfolio'),
                get_comment_date(),
                get_comment_time()
            );
            ?>
        </a>
        <?php edit_comment_link(__('Edit', 'asad-portfolio'), '<span class="edit-link">', '</span>'); ?>
    </div>

    <div class="comment-content">
        <?php comment_text(); ?>
    </div>

    <div class="reply">
        <?php
        comment_reply_link(array_merge($args, array(
            'add_below' => $add_below,
            'depth'     => $depth,
            'max_depth' => $args['max_depth'],
        )));
        ?>
    </div>

    <?php if ('div' != $args['style']) : ?>
        </div>
    <?php endif; ?>
    <?php
}

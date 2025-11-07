<?php
/**
 * Custom search form template
 *
 * @package Asad_Portfolio_Manager
 */
?>

<form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
    <div class="search-form-wrapper">
        <label for="s" class="screen-reader-text"><?php _e('Search for:', 'asad-portfolio'); ?></label>
        <div class="search-input-group">
            <input type="search"
                   id="s"
                   class="search-field"
                   placeholder="<?php echo esc_attr_x('Search...', 'placeholder', 'asad-portfolio'); ?>"
                   value="<?php echo get_search_query(); ?>"
                   name="s"
                   required />
            <button type="submit" class="search-submit">
                <i class="fas fa-search"></i>
                <span class="screen-reader-text"><?php _e('Search', 'asad-portfolio'); ?></span>
            </button>
        </div>
    </div>
</form>

<style>
.search-form {
    margin: 1rem 0;
}

.search-form-wrapper {
    position: relative;
}

.search-input-group {
    display: flex;
    gap: 0;
    position: relative;
}

.search-field {
    flex: 1;
    padding: 0.75rem 1rem;
    border: 2px solid var(--border-color);
    border-radius: 4px 0 0 4px;
    font-size: 1rem;
    background: var(--bg-color);
    color: var(--text-color);
    transition: border-color 0.3s;
}

.search-field:focus {
    outline: none;
    border-color: var(--primary-color);
}

.search-submit {
    padding: 0.75rem 1.5rem;
    background: var(--primary-color);
    color: #fff;
    border: 2px solid var(--primary-color);
    border-radius: 0 4px 4px 0;
    cursor: pointer;
    transition: all 0.3s;
    font-size: 1rem;
}

.search-submit:hover {
    background: var(--secondary-color);
    border-color: var(--secondary-color);
}

.search-submit:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.3);
}

/* Responsive */
@media (max-width: 480px) {
    .search-field {
        font-size: 0.875rem;
        padding: 0.5rem 0.75rem;
    }

    .search-submit {
        padding: 0.5rem 1rem;
    }
}
</style>

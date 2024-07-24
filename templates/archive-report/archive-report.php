<?php
/**
 * The template for displaying archive-report pages.
 *
 * Used to display archive-report-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since   Timber 0.2
 */

$context = Timber::context();

$context['title'] = 'archive-report';
$context['terms'] = get_terms([
    'taxonomy'   => 'years',
    'hide_empty' => false,
    'parent'     => 0,
    'order'      => 'DESC',
]);

Timber::render('archive-report.twig', $context);

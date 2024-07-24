<?php

defined('ABSPATH') || exit;

class AdvancedSearch
{

    public static function preparePostData($posts)
    {
        $reports = array_map(function($post) {
			$post_link = $post->post_parent !== 0 ? get_permalink($post->post_parent) : get_permalink($post->ID);

			return [
				'post_link'    => $post_link,
				'post_title'   => $post->post_title,
				'post_excerpt' => wp_trim_words(get_field('intro', $post->post_parent), 17, ''),
			];
		}, $posts);

        return $reports;
    }

    public static function getReportCount($inputArgs, $search)
    {
        $extraArgs = [
            'posts_per_page'      => -1,
            'fields'              => 'ids',
            'post_parent__not_in' => [0],
            'search_title'        => $search,
        ];
    
        $args = array_merge($inputArgs, $extraArgs);
    
        add_filter( 'posts_where', 'title_filter', 10, 2 );

        $query = new WP_Query ($args);

        remove_filter( 'posts_where', 'title_filter', 10 );

        return $query->post_count;
    }

    public static function searchByParamsAndTitle($args, $search, $taxonomies, $postData)
    {
        $tax_query = ['relation' => 'AND',];
        foreach($postData as $key => $value) {
            if ( in_array($key, $taxonomies) ) {
                array_push($tax_query, [
                    'taxonomy' => $key,
                    'field'    => 'term_id',
                    'terms'    => explode(',', $value),
                ]);
            }
        }
        $args['tax_query'] = $tax_query;
    
        if(!empty($search)) $args['search_title'] = $search;
    
        return $args;
    
    }

    public static function getSignatoryCount($search, $postData)
    {
        $args = [
            'taxonomy'               => 'signatory',
            'number'                 => 0, // all
            'hide_empty'             => true,
            'name__like'             => $search,
            'fields'                 => 'ids',
            'update_post_term_cache' => false, // Improves Query performance
            'update_post_meta_cache' => false, // Improves Query performance
        ];
    
        if(!empty($postData['signatory'])) $args['include'] = $postData['signatory'];
    
        $terms = new WP_Term_Query ($args);
    
        return isset($terms->terms) ? count($terms->terms) : 0;
    }

    public static function searchSignatory($search, $offset, $postData)
    {
        $reports = [];
    
        // Get terms
        $args = [
            'taxonomy'               => ['signatory'],
            'offset'                 => $offset,
            'number'                 => 6,
            'hide_empty'             => true,
            'update_post_term_cache' => false, // Improves Query performance
            'update_post_meta_cache' => false, // Improves Query performance
          ];
    
        if(!empty($search)) $args['name__like'] = $search;
    
        $include = isset($postData['signatory']) ? explode(",", $postData['signatory']) : [];
    
        if(!empty($postData['signatory'])) $args['include'] = $include;
    
        $terms = new WP_Term_Query($args);
    
        if($terms->terms) {
            $reports = array_map(function($term) {
                $mainPost = get_posts([
                    'posts_per_page' => -1,
                    'post_parent' => 0,
                    'post_type'      => 'signatory-report',
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'signatory',
                            'field'    => 'term_id',
                            'terms'    => $term->term_id,
                        )
                    )
                ]);
        
                return [
                    'post_link'    => get_the_permalink($mainPost[0]->ID),
                    'post_title'   => $term->name,
                    'post_excerpt' => wp_trim_words($term->description, 17, ''),
                ];
            }, $terms->terms);
        }
    
        return $reports;
    }

    public static function searchByTermsTitle($args, $search, $taxonomies)
    {
        $terms = get_terms(
            [
                'taxonomy'   => $taxonomies,
                'hide_empty' => false,           
                'name__like' => $search,
            ]
        );
    
        if($terms) {
            $tax_query = ['relation' => 'OR',];
    
            foreach($terms as $value) {
                array_push($tax_query, [
                    'taxonomy' => $value->taxonomy,
                    'field'    => 'term_id',
                    'terms'    => explode(',', $value->term_id),
                ]);
            }
    
            $args['tax_query'] = $tax_query;
        }
    
        return $terms ? $args : [];
    }

    public static function mergeValues($arr1, $arr2) {
        $merged = $arr1;
        
        if( is_array($arr2) ) {
            foreach($arr2 as $key => $value) {
                if (in_array(true, self::checkArrayChild($value))) {
                    $merged[$key] = [];
                    $merged[$key] = self::mergeValues($merged[$key], $value);
                } else {
                    $merged[$key] = is_array($value) ? array_unique($value) : $value;
                }
            }
        }
        
        return $merged;
    }

    public static function checkArrayChild($child) {
        $type_array = [];
    
        if ( is_array($child) ) {
            foreach($child as $value) {
                if ( is_array($value) ) {
                    $type_array[] = 1;
                } else {
                    $type_array[] = 0;
                }
            }
        } else {
            $type_array[] = 0;
        }
        return $type_array;
    }
}

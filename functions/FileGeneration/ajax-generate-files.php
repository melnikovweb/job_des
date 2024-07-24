<?php

/**
 * WP-Ajax handler for generate files
 */

 if( ! function_exists('generate_files')) {
    add_action( 'wp_ajax_generate_files', 'generate_files' );
    add_action( 'wp_ajax_nopriv_generate_files', 'generate_files' );

    function generate_files() {
		$fileType   = !empty($_POST['fileType']) ? $_POST['fileType'] : '';
		$childPost  = !empty($_POST['childPost']) ? $_POST['childPost'] : '';
		$parentPost = !empty($_POST['parentPost']) ? $_POST['parentPost'] : '';
		$id         = $childPost ? $childPost : $parentPost;

		$data = DataCollection::generateData($childPost, '', $parentPost);

		switch ($fileType) {
			case 'json':
			$response = [
				$fileType => FileGeneration::generateJSON($data, true),
			];
			break;
			case 'csv':
				$response = [
					$fileType => FileGeneration::generateCSV($data),
				];
			break;
			case 'pdf':
				$response = [
					$fileType => FileGeneration::getPDF($id),
				];
			break;
			default:
				$response = [
					'error' => 'Something wrong',
				];
		}

        wp_send_json( $response );
        die;
    }
}

<?php
/**
 * BoldGrid Source Code
 *
 * @package   Boldgrid_Inspirations_Attachment.
 * @copyright BoldGrid.com
 * @version   $Id$
 * @author    BoldGrid.com <wpb@boldgrid.com>
 */

/**
 * An attachment utility class.
 *
 * @since 1.4.8
 */
class Boldgrid_Inspirations_Attachment {

	/**
	 * Check if a size exists for an attachment.
	 *
	 * @since 1.4.8
	 *
	 * @param  int $id     Attachment id.
	 * @param  int $width
	 * @param  int $height
	 * @return boolean
	 */
	public static function size_exists( $id, $width, $height ) {
		$width = (int) $width;
		$height = (int) $height;

		$src = wp_get_attachment_image_src( $id, array( $width, $height ) );

		return is_array( $src ) && $width === $src[1] && $height === $src[2];
	}

	/**
	 * Resize an attachment and add info to metadata.
	 *
	 * @since 1.4.8
	 *
	 * @param  int     $id     Attachment id.
	 * @param  int     $width
	 * @param  int     $height
	 * @return boolean True if on success.
	 */
	public static function resize( $id, $width, $height ) {
		$width = (int) $width;
		$height = (int) $height;

		$suffix = $width . 'x' . $height;

		if( self::size_exists( $id, $width, $height ) ) {
			return true;
		}

		$filepath = get_attached_file( $id );

		$image = wp_get_image_editor( $filepath );

		// Generate new filename.
		$pathinfo = pathinfo( $filepath );
		$new_filepath = $image->generate_filename( $suffix, $pathinfo['dirname'], $pathinfo['extension'], $pathinfo['filename'] );

		$is_resized = $image->resize( $width, $height, true );
		if ( is_wp_error( $is_resized ) ) {
			return false;
		}

		$is_saved = $image->save( $new_filepath );
		if ( is_wp_error( $is_saved ) ) {
			return false;
		}

		// Get and update the attachment's metadata with this new size.
		$metadata = wp_get_attachment_metadata( $id );

		if( empty( $metadata['sizes'][$suffix] ) ) {
			$metadata['sizes'][$suffix] = array(
				'file' => basename( $new_filepath ),
				'width' => $width,
				'height' => $height,
				'mime-type' => 'image/jpeg',
			);

			wp_update_attachment_metadata( $id, $metadata );
		}

		return true;
	}
}
<?php
/**
 * BoldGrid Source Code
 *
 * @package Boldgrid_Plugintest
 * @copyright BoldGrid.com
 * @version $Id$
 * @author BoldGrid.com <wpb@boldgrid.com>
 */

/**
 * BoldGrid Library Reseller Test class.
 *
 * @since
 */
class Test_BoldGrid_Libarary_Reseller extends WP_UnitTestCase {

	/**
	 * Test default reseller data.
	 */
	public function testDefaults() {
		delete_option( 'boldgrid_reseller' );

		$reseller = new Boldgrid\Library\Library\Reseller();

		// With no reseller data, check that the defaults are being returned.
		$this->assertSame( $reseller->centralUrl, $reseller->data['reseller_coin_url'] );

		update_option( 'boldgrid_reseller', array(
			'reseller_coin_url' => 'https://boldgrid.com/reseller-coin-url',
		));

		// With reseller data set, ensure defaults are not returned.
		$this->assertSame( 'https://boldgrid.com/reseller-coin-url', $reseller->data['reseller_coin_url'] );
	}
}

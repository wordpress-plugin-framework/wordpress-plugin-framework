<?php

declare(strict_types=1);

namespace WordPressPluginFramework\Pipeline\Middlewares\CurrentUserCan\Capability;

enum Capability: string
{
	case ManageWooCommerce = 'manage_woocommerce';
}
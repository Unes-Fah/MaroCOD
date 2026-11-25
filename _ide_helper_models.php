<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Order> $orders
 * @property-read int|null $orders_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CarrierRate> $rates
 * @property-read int|null $rates_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Carrier newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Carrier newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Carrier query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Carrier whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Carrier whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Carrier whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Carrier whereUpdatedAt($value)
 */
	class Carrier extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $carrier_id
 * @property string $city
 * @property numeric $price
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Carrier $carrier
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarrierRate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarrierRate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarrierRate query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarrierRate whereCarrierId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarrierRate whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarrierRate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarrierRate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarrierRate wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarrierRate whereUpdatedAt($value)
 */
	class CarrierRate extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $store_name
 * @property string $email
 * @property string $password
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Order> $orders
 * @property-read int|null $orders_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Merchant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Merchant newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Merchant query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Merchant whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Merchant whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Merchant whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Merchant whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Merchant wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Merchant whereStoreName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Merchant whereUpdatedAt($value)
 */
	class Merchant extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $merchant_id
 * @property string $customer_name
 * @property string $customer_phone
 * @property string $city
 * @property numeric $total_amount
 * @property string $status
 * @property string|null $tracking_number
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $carrier_id
 * @property numeric|null $shipping_cost
 * @property-read \App\Models\Carrier|null $carrier
 * @property-read \App\Models\Merchant $merchant
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereCarrierId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereCustomerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereCustomerPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereMerchantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereShippingCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereTrackingNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereUpdatedAt($value)
 */
	class Order extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}


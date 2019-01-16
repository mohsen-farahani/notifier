<?php

namespace Asanbar\Notifier\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="Notification",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="user_id",
 *          description="user_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="identifier",
 *          description="identifier",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="title",
 *          description="title",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="body",
 *          description="body",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="type",
 *          description="type",
 *          type="boolean"
 *      ),
 *      @SWG\Property(
 *          property="provider_name",
 *          description="provider_name",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="try",
 *          description="try",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="error",
 *          description="error",
 *          type="string"
 *      )
 * )
 */
class Notification extends Model
{
    public $table = 'notifications';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $dates = ['deleted_at'];

    public $fillable = [
        'user_id',
        'identifier',
        'title',
        'body',
        'type',
        'provider_name',
        'expire_at',
        'queued_at',
        'success_at',
        'try',
        'error',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'            => 'integer',
        'user_id'       => 'integer',
        'identifier'    => 'string',
        'title'         => 'string',
        'body'          => 'string',
        'type'          => 'boolean',
        'provider_name' => 'string',
        'error'         => 'string',
    ];

}

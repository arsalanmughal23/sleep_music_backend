<!-- Id Field -->
<dt>{!! Form::label('id', 'Id:') !!}</dt>
<dd>{!! $userSubscription->id !!}</dd>

<!-- Trial Field -->
<dt>{!! Form::label('offer_discount_type', 'Trial:') !!}</dt>
<dd>{!! $userSubscription->offer_discount_type ?? '-' !!}</dd>

<!-- User Id Field -->
<dt>{!! Form::label('user_id', 'User Id:') !!}</dt>
<dd>{!! $userSubscription->user_id !!}</dd>

<!-- Amount Field -->
<dt>{!! Form::label('amount', 'Amount:') !!}</dt>
<dd>{!! $userSubscription->amount !!}</dd>

<!-- Currency Field -->
<dt>{!! Form::label('currency', 'Currency:') !!}</dt>
<dd>{!! $userSubscription->currency !!}</dd>

<!-- (Transaction) Reference Key Field -->
<!-- <dt>{!! Form::label('reference_key', 'Transaction Reference:') !!}</dt>
<dd>{!! $userSubscription->reference_key !!}</dd> -->

<!-- Platform Field -->
<dt>{!! Form::label('platform', 'Platform:') !!}</dt>
<dd>{!! $userSubscription->platform !!}</dd>

<!-- Expiry Data Field -->
<dt>{!! Form::label('expiry_date', 'Expiry Date:') !!}</dt>
<dd>{!! $userSubscription->expiry_date !!}</dd>

<!-- Product Id At Field -->
<dt>{!! Form::label('product_id', 'Product Id:') !!}</dt>
<dd>{!! $userSubscription->product_id !!}</dd>

<!-- Status Field -->
<dt>{!! Form::label('status', 'Status:') !!}</dt>
<dd>{!! $userSubscription->status_badge !!}</dd>

<!-- Created At Field -->
<dt>{!! Form::label('created_at', 'Created At:') !!}</dt>
<dd>{!! $userSubscription->created_at !!}</dd>

<!-- Updated At Field -->
<dt>{!! Form::label('updated_at', 'Updated At:') !!}</dt>
<dd>{!! $userSubscription->updated_at !!}</dd>


<!-- Id Field -->
<dt>{!! Form::label('id', 'Id:') !!}</dt>
<dd>{!! $transaction->id !!}</dd>

<!-- User Id Field -->
<dt>{!! Form::label('user_id', 'User Id:') !!}</dt>
<dd>{!! $transaction->user_id !!}</dd>

<!-- Currency Field -->
<dt>{!! Form::label('currency', 'Currency:') !!}</dt>
<dd>{!! $transaction->currency !!}</dd>

<!-- Amount Field -->
<dt>{!! Form::label('amount', 'Amount:') !!}</dt>
<dd>{!! $transaction->amount !!}</dd>

<!-- Transaction Data Field -->
<dt>{!! Form::label('transaction_date', 'Transaction Date:') !!}</dt>
<dd>{!! $transaction->transaction_date !!}</dd>

<!-- Description Field -->
<dt>{!! Form::label('description', 'Description:') !!}</dt>
<dd>{!! $transaction->description !!}</dd>

<!-- Status Field -->
<dt>{!! Form::label('status', 'Status:') !!}</dt>
<dd>{!! $transaction->status_badge !!}</dd>

<!-- Created At Field -->
<dt>{!! Form::label('created_at', 'Created At:') !!}</dt>
<dd>{!! $transaction->created_at !!}</dd>

<!-- Updated At Field -->
<dt>{!! Form::label('updated_at', 'Updated At:') !!}</dt>
<dd>{!! $transaction->updated_at !!}</dd>


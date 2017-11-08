<?php
namespace App\Exports;
use App\Models\Transaction;
use App\Http\Resources\TransactionResource;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Http\Request;

use App\Models\MasterTransactionType as MasterTransactionTypeModel;
use App\Models\PaymentMethod as PaymentMethodModel;
use App\Models\Account as AccountModel;
use Maatwebsite\Excel\Concerns\FromCollection;




use Carbon\Carbon;

class TransactionExport implements FromView
{
   
    public function __construct(array $data = [], Request $request)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        $from_created_date = $this->data['from_created_date'];
        $to_created_date = $this->data['to_created_date'];
        $account = $this->data['account'];
        $transaction_type = $this->data['transaction_type'];
        $payment_method = $this->data['payment_method'];
        $bill_to = $this->data['bill_to'];

        $query = Transaction::query();

        if($from_created_date != ''){
            $from_created_date = strtotime($from_created_date);
            $from_created_date = date(config('app.sql_date_format'), $from_created_date);
            $from_created_date = $from_created_date . ' 00:00:00';
            $query = $query->where('transactions.created_at', '>=', $from_created_date);
        }
        if($to_created_date != ''){
            $to_created_date = strtotime($to_created_date);
            $to_created_date = date(config('app.sql_date_format'), $to_created_date);
            $to_created_date = $to_created_date . ' 23:59:59';
            $query = $query->where('transactions.created_at', '<=', $to_created_date);
        }
        if(isset($account)){
            $account_data = AccountModel::select('id')
            ->where('slack', '=', trim($account))
            ->first();

            $query = $query->where('transactions.account_id', $account_data->id);
        }

        if(isset($transaction_type)){
            $transaction_type_data = MasterTransactionTypeModel::select('id')
            ->where('transaction_type_constant', '=', trim($transaction_type))
            ->first();

            $query = $query->where('transactions.transaction_type', $transaction_type_data->id);
        }

        if(isset($payment_method)){
            
            $payment_method_data = PaymentMethodModel::select('id', 'label')
            ->where('slack', '=', trim($payment_method))
            ->first();

            $query = $query->where('transactions.payment_method_id', $payment_method_data->id);
        }

        if(isset($bill_to)){
            $query = $query->where('transactions.bill_to', $bill_to);
        }

        $transactions = $query->get();

        $transaction_report_data = [];
        
        if(count($transactions)>0){
            foreach($transactions as $key => $transaction_item){
                $transaction = collect(new TransactionResource($transaction_item));
                $transaction_report_data[$key] = [
                    'transaction_date'=>(isset($transaction['transaction_date']))?$transaction['transaction_date']:'',
                    'transaction_code'=>(isset($transaction['transaction_code']))?$transaction['transaction_code']:'',
                    'account_code'=>(isset($transaction['account']['account_code']))?$transaction['account']['account_code']:'',
                    'account_name'=>(isset($transaction['account']['label']))?$transaction['account']['label']:'',
                    'transaction_type'=>(isset($transaction['transaction_type_data']['label']))?$transaction['transaction_type_data']['label']:'',
                    'payment_method'=>(isset($transaction['payment_method']))?$transaction['payment_method']:'',
                    'payment_for'=>(isset($transaction['bill_to']))?$transaction['bill_to']:'',
                    'bill_to_name'=>(isset($transaction['bill_to_name']))?$transaction['bill_to_name']:'',
                    'bill_to_contact'=>(isset($transaction['bill_to_contact']))?$transaction['bill_to_contact']:'',
                    'bill_to_address'=>(isset($transaction['bill_to_address']))?$transaction['bill_to_address']:'',
                    'currency_code'=>(isset($transaction['currency_code']))?$transaction['currency_code']:'',
                    'total_amount'=>(isset($transaction['amount']))?$transaction['amount']:'',
                    'payment_gateway_transaction_id'=>(isset($transaction['pg_transaction_id']))?$transaction['pg_transaction_id']:'',
                    'payment_gateway_transaction_status'=>(isset($transaction['pg_transaction_status']))?$transaction['pg_transaction_status']:'',
                    'created_at'=>(isset($transaction['created_at_label']))?$transaction['created_at_label']:'',
                    'created_by'=>(isset($transaction['created_by']['fullname']))?$transaction['created_by']['fullname']:''
                 ];
            }
        }
        return view('report.exports.transaction_report', [
            'transaction_report_data' => $transaction_report_data
        ]);
    }
}

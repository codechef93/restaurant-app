<?php
namespace App\Exports;
use App\Models\User;
use App\Http\Resources\UserResource;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Http\Request;

use Carbon\Carbon;

class UserExport implements FromView
{
   
    public function __construct(array $data = [], Request $request)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        $from_created_date = $this->data['from_created_date'];
        $to_created_date = $this->data['to_created_date'];
        $role = $this->data['role'];
        $status = $this->data['status'];
        

        $query = User::query()
        ->select('users.*', 'roles.id')
        ->roleJoin()
        ->hideSuperAdminRole();

        if($from_created_date != ''){
            $from_created_date = strtotime($from_created_date);
            $from_created_date = date(config('app.sql_date_format'), $from_created_date);
            $from_created_date = $from_created_date . ' 00:00:00';
            $query = $query->where('users.created_at', '>=', $from_created_date);
        }
        if($to_created_date != ''){
            $to_created_date = strtotime($to_created_date);
            $to_created_date = date(config('app.sql_date_format'), $to_created_date);
            $to_created_date = $to_created_date . ' 23:59:59';
            $query = $query->where('users.created_at', '<=', $to_created_date);
        }
        if($role != ''){
            $query = $query->where('roles.slack', $role);
        }
        if(isset($status)){
            $query = $query->where('users.status', $status);
        }

        $users = $query->get();

        $user_report_data = [];
        
        if(count($users)>0){
            foreach($users as $key => $user_item){
                $user = collect(new UserResource($user_item));
                $user_report_data[$key] = [
                    'user_code'=>(isset($user['user_code']))?$user['user_code']:'',
                    'fullname'=>(isset($user['fullname']))?$user['fullname']:'',
                    'email'=>(isset($user['email']))?$user['email']:'',
                    'phone'=>(isset($user['phone']))?$user['phone']:'',
                    'role'=>(isset($user['role']['label']))?$user['role']['label']:'',
                    'status'=>(isset($user['status']['label']))?$user['status']['label']:'',
                    'created_at'=>(isset($user['created_at_label']))?$user['created_at_label']:'',
                    'created_by'=>(isset($user['created_by']['fullname']))?$user['created_by']['fullname']:'',
                    'updated_at'=>(isset($user['updated_at_label']))?$user['updated_at_label']:'',
                    'updated_by'=>(isset($user['updated_by']['fullname']))?$user['updated_by']['fullname']:''
                    
                 ];
            }
        }
        return view('report.exports.user_report', [
            'user_report_data' => $user_report_data
        ]);
    }
}

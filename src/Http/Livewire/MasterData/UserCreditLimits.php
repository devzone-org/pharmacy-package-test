<?php

namespace Devzone\Pharmacy\Http\Livewire\MasterData;

use Illuminate\Support\Facades\DB;
use Livewire\Component;
use App\Models\User;

class UserCreditLimits extends Component
{
    public $success;
    public $users=[];
    public function mount(){
        $this->users=User::from('users as u')
            ->join('chart_of_accounts as coa','coa.id','=','u.account_id')
            ->select('coa.name','u.id',DB::raw('floor(u.credit_limit) as credit_limit'),'u.account_id')
            ->get()->toArray();
    }
    public function render(){
        return view('pharmacy::livewire.master-data.user-credit-limits');
    }
    public function create(){
        DB::beginTransaction();
        try {
            foreach ($this->users as $key=>$user){
                User::where('id',$user['id'])->update([
                    'credit_limit'=>!empty($user['credit_limit']) ? $user['credit_limit'] : 0,
                ]);
            }
            DB::commit();
            $this->success='Updated successfully';
        }catch (\Exception $e){
            $this->addError('Exception',$e->getMessage());
            DB::rollBack();
        }

    }
}

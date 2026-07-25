<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class DriversExport implements FromView, WithEvents
{
    protected $request;
    public function __construct($request)
    {
        $this->request = $request;

    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function view(): View
    {
        $request = $this->request;
       $items= User::Drivers()->FilterDashbordDrivers([
            'search_name'=> $request->search_name,
            'search_mobile'=> $request->search_mobile,
            'search_email'=>   $request->search_email,
            'search_active'=>   $request->search_active
             ])->select('id', 'name', 'mobile', 'email', 'is_active', 'created_at')->get();

            return view('dashboard.drivers.excel.index', [
            'items' => $items
        ]);
    }

      // 👇 هذه الدالة هي المفتاح لتفعيل RTL
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getDelegate()->setRightToLeft(true);
            },
        ];
    }
}

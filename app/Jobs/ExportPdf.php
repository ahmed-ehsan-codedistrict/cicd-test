<?php

namespace App\Jobs;

use App\Events\pdfExportedEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use PDF;
use Illuminate\Support\Facades\Storage;
use App\Models\CustomNotification;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProductExports;
use App\Exports\ProductPerSheetExport;

class ExportPdf implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected  $data;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        $data
    ) {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->data['format'] == "excel") {
            $fileName =  $this->data['fileName'] . '.xlsx';
            if ($this->data['linesheetId'] > 0) {
                Excel::store(new ProductPerSheetExport($this->data), "public/pdf/$fileName");
            } else {
                Excel::store(new ProductExports($this->data, $this->data['fileName']), "public/pdf/$fileName");
            }
            // Notification message to user
            $message = "Excel file has been exported";
            $downloadUrl = $this->data['url'] . '/storage/pdf/' . $this->data['fileName'] . '.pdf';
            //save the notification in database
            $Message = "Excel file has been exported. Click <a href='$downloadUrl'>here</a> to download";
        } else {
            // share data array with view
            view()->share($this->data['viewName'], ["pdf" => $this->data]);
            $pdf = PDF::loadView($this->data['viewName'], ["pdf" => $this->data])
                ->setPaper($this->data['size'], $this->data['orientation']);

            //store the pdf and generate the dowloand link
            $content = $pdf->download()->getOriginalContent();
            Storage::put('public/pdf/' . $this->data['fileName'] . '.pdf', $content);
            $downloadUrl = $this->data['url'] . '/storage/pdf/' . $this->data['fileName'] . '.pdf';

            // Notification message to user
            $message = "PDF has been exported";

            //save the notification in database
            $Message = "PDF has been generated. Click <a href='$downloadUrl'>here</a> to download";
        }

        event(new pdfExportedEvent($message));
        CustomNotification::create([
            "UserId" => $this->data['userId'],
            "CompanyNo" => $this->data['companyNo'],
            "Message" => $Message
        ]);
    }

    public function failed($exception = null)
    {

        $message = "oops! something went wrong with exporting file. Please try again";

        //Send notification if job failed
        event(new pdfExportedEvent($message));

        // save notification in database
        CustomNotification::create([
            "UserId" => $this->data['userId'],
            "CompanyNo" => $this->data['companyNo'],
            "Message" => $exception
        ]);
    }
}

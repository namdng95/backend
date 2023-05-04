<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Bus\Queueable;
use App\Mail\ZipFileMail;
use Throwable;

class SendZipFileEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Email
     *
     * @var array
     */
    protected array $data;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $receiver = $this->data['email'];
        $dataMail = [
            'title' => 'Congratulation!!!',
            'zip_name' => $this->data['zip_name'],
            'file_path' => $this->data['file_path'],
            'body' => $this->parseBodyMail(),
        ];

        Mail::to($receiver)->send(new ZipFileMail($dataMail));

        if (
            Storage::disk('local')->exists($dataMail['zip_name'])
            && is_dir(storage_path("app/files"))
        ) {
            // Delete local files and folder
            Storage::disk('local')->delete($dataMail['zip_name']);
            Storage::disk('local')->deleteDirectory('files');
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(Throwable $exception): void
    {
        Log::debug("Error: {$exception->getMessage()}");
    }

    /**
     * Parse Body Mail
     *
     * @return string
     */
    private function parseBodyMail(): string
    {
        $code = $this->data['code'];
        $name = $this->data['name'];
        $url = config('custom.web_url') . '/laradock/' . $code . '/detail';
        $applied_at = now()->format('Y年m月d日H時i分');

        $body = "※ このメールは「jinjer請求書」からの自動配信メールとなっております。
            ご返信はお受けできかねますのでご了承ください。

            {$name}様
        ";

        $body .= "\n請求書番号{$code}の申請が否認されました。\n";

        $body .= "
            ＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝
            請求書番号：{$code}
            帳票種別：請求書
            申請日時：{$applied_at}
            ＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝

            申請内容はこちらになります。クリックすると内容が別ウィンドウで表示されます。
        ";

        $body .= "<a target='_blank' href='{$url}'>{$url}</a>";

        return $body;
    }
}

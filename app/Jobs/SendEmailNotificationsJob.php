<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Bus\Queueable;
use App\Mail\WelcomeEmail;
use App\Models\User;

class SendEmailNotificationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * User model
     *
     * @var User
     */
    protected User $user;

    /**
     * Construct.
     *
     * @param User $user User
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $receiver = $this->user->email;
        $dataMail = [
            'title' => "【Company請求書】請求書番号{$this->user['code']}の申請が否認されました",
            'body' => $this->parseBodyMail($this->user->id, $this->user->code),
        ];

        Mail::to($receiver)->send(new WelcomeEmail($dataMail));
    }

    /**
     * Parse Body Mail
     *
     * @param int $userId   User ID
     * @param int $userCode User Code
     *
     * @return string
     */
    private function parseBodyMail(int $userId, int $userCode): string
    {
        $url = config('custom.web_url') . '/invoices/' . $userId . '/detail';
        $applied_at = now()->format('Y年m月d日H時i分');

        $body = "※ このメールは「Company請求書」からの自動配信メールとなっております。
            ご返信はお受けできかねますのでご了承ください。

            {$this->user->name}様
        ";

        $body .= "\n請求書番号{$this->user->code}の申請が否認されました。\n";

        $body .= "
            ＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝
            請求書番号：{$userCode}
            帳票種別：請求書
            申請日時：{$applied_at}
            ＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝

            申請内容はこちらになります。クリックすると内容が別ウィンドウで表示されます。
        ";

        $body .= "<a target='_blank' href='{$url}'>{$url}</a>";

        return $body;
    }
}

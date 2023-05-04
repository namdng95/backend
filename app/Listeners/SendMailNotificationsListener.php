<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use App\Events\SendMailNotificationsEvent;
use App\Mail\WelcomeEmail;

class SendMailNotificationsListener
{
    /**
     * Handle the event.
     *
     * @param SendMailNotificationsEvent $event Send Mail Notifications Event
     *
     * @return void
     */
    public function handle(SendMailNotificationsEvent $event): void
    {
        $user = $event->user;
        $receiver = $user->email;
        $dataMail = [
            'title' => "【Company請求書】請求書番号{$user->code}の申請が否認されました",
            'body' => $this->parseBodyMail($user->name, $user->id, $user->code),
        ];

        Mail::to($receiver)->send(new WelcomeEmail($dataMail));
    }

    /**
     * Parse Body Mail
     *
     * @param string $userName User Name
     * @param int    $userId   User ID
     * @param int    $userCode User Code
     *
     * @return string
     */
    private function parseBodyMail(string $userName, int $userId, int $userCode): string
    {
        $url = config('custom.web_url') . '/invoices/' . $userId . '/detail';
        $applied_at = now()->format('Y年m月d日H時i分');

        $body = "※ このメールは「Company請求書」からの自動配信メールとなっております。
            ご返信はお受けできかねますのでご了承ください。

            {$userName}様
        ";

        $body .= "\n請求書番号{$userCode}の申請が否認されました。\n";

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

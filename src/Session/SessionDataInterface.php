<?php
namespace Praline\Session;

// 放進 Session data 中的物件必須實作這個介面，
//  1. 讓 SessionManager 能在使用者登入時，清除其舊有的 session 資料
//  2. 方便在輸出的 log 之中篩選使用者的行為
interface SessionDataInterface
{
    // 認證成功後，資料會放在這個屬性內，可以由 $request->getAttribute() 取得
    public const ATTRIBUTE_NAME = 'sessionData';

    // 傳回可用來唯一識別此資料的字串
    public function getUniqueKey(): string;

    // 傳回一個在 log 中方便識別此資料的字串
    public function getDescription(): string;
}

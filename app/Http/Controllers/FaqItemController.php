<?php

namespace App\Http\Controllers;

use App\Models\FaqItem;
use Illuminate\Http\Response;

class FaqItemController extends Controller
{
    public function index(): Response
    {
        return response(FaqItem::all());
    }

    public function store(): Response
    {
        $this->authorize(FaqItem::class);
        return response(FaqItem::query()->create(request()->all()));
    }

    public function update(FaqItem $faqItem): Response
    {
        $this->authorize($faqItem);
        return response(tap($faqItem)->update(request()->all()));
    }

    public function destroy(FaqItem $faqItem): Response
    {
        $this->authorize($faqItem);
        return response($faqItem->delete());
    }

    public function getFaqsByLang(string $lang): Response
    {
        return response(FaqItem::query()
            ->select(['id', "question_$lang as question", "answer_$lang as answer"])
            ->get());
    }
}

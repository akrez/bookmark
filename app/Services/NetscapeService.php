<?php

namespace App\Services;

use App\Support\ApiResponse;
use DOMElement;
use Illuminate\Support\Facades\Validator;

class NetscapeService extends Service
{
    public function import(array $input): ApiResponse
    {
        $validator = Validator::make($input, [
            'html' => ['required', 'string'],
        ]);
        if ($validator->fails()) {
            return ApiResponse::makeFromValidator($validator);
        }

        $bookmarks = [];

        try {

            libxml_use_internal_errors(true);
            $doc = new \DOMDocument;
            $doc->loadHTML($input['html']);
            libxml_clear_errors();

            $xpath = new \DOMXPath($doc);

            $links = $xpath->query('//a[@href]');

            /** @var DOMElement $link */
            foreach ($links as $link) {
                $href = $link->getAttribute('href');
                if (! preg_match('#^https?://#i', $href)) {
                    continue;
                }

                $bookmarks[] = [
                    'href' => $href,
                    'title' => trim($link->nodeValue) ?: null,
                    'add_date' => $link->getAttribute('add_date') ?: null,
                    'icon' => $link->getAttribute('icon') ?: null,
                ];
            }
        } catch (\Throwable $th) {
            return ApiResponse::make(500);
        }

        return ApiResponse::make(200)->data([
            'bookmarks' => $bookmarks,
        ]);
    }

    public function export(int $userId, array $payload)
    {
        $indexResponse = BookmarkService::new()->index($userId, $payload, false);
        if (! $indexResponse->isSuccessful()) {
            return ApiResponse::make($indexResponse->getStatus());
        }

        $bookmarksGroups = collect($indexResponse->getData('bookmarks'))
            ->groupBy('collection')
            ->sortKeys();

        $groups = [];
        foreach ($bookmarksGroups as $collection => $bookmarksGroup) {
            $lines = [
                '<DT><H3>'.e($collection).'</H3>',
                '<DL><p>',
            ];
            foreach ($bookmarksGroup as $bookmark) {
                $attrs = [
                    'HREF="'.e($bookmark['url']['url']).'"',
                    'ADD_DATE="'.($bookmark['created_at'] ? strtotime($bookmark['created_at']) : '').'"',
                ];

                $lines[] = sprintf(
                    '<DT><A %s>%s</A>',
                    implode(' ', $attrs),
                    e($bookmark['url']['title'] ?: $bookmark['url']['url'])
                );

                if ($bookmark['note']) {
                    $lines[] = '<DD>'.e($bookmark['note']);
                }
            }
            $lines[] = '</DL><p>';
            $groups[] = implode("\n", $lines);
        }

        return ApiResponse::make()->data([
            'file_name' => 'bookmarks_'.date('Y_m_d_H_i_s'),
            'file' => implode("\n", [
                '<!DOCTYPE NETSCAPE-Bookmark-file-1>',
                '<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=UTF-8">',
                '<TITLE>Bookmarks</TITLE>',
                '<H1>Bookmarks</H1>',
                '<DL><p>',
                implode("\n", $groups),
                '</DL><p>',
            ]),
        ]);
    }
}

<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

const POST_VIEWS_FIELD = 'views';

function normalizeViewFields(): void
{
    static $normalized = false;
    if ($normalized) return;
    $normalized = true;

    $db = \Typecho\Db::get();
    $rows = $db->fetchAll(
        $db->select('cid', 'type', 'str_value', 'int_value')
            ->from('table.fields')
            ->where('name = ?', POST_VIEWS_FIELD)
            ->where('type <> ?', 'str')
    );

    foreach ($rows as $row) {
        $value = max((int) $row['int_value'], (int) $row['str_value']);
        $db->query(
            $db->update('table.fields')
                ->rows(array('type' => 'str', 'str_value' => (string) $value, 'int_value' => 0))
                ->where('cid = ?', (int) $row['cid'])
                ->where('name = ?', POST_VIEWS_FIELD)
        );
    }
}

function getPostViews(int $cid): int
{
    normalizeViewFields();
    $db = \Typecho\Db::get();
    $row = $db->fetchRow(
        $db->select('str_value')
            ->from('table.fields')
            ->where('cid = ?', $cid)
            ->where('name = ?', POST_VIEWS_FIELD)
            ->limit(1)
    );
    return $row ? (int) $row['str_value'] : 0;
}

function recordPostView(\Widget\Archive $archive): int
{
    $cid = (int) $archive->cid;
    if ($cid < 1 || !$archive->is('post')) return 0;
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') return getPostViews($cid);
    if (preg_match('/bot|spider|crawler|slurp|bingpreview/i', $_SERVER['HTTP_USER_AGENT'] ?? '')) {
        return getPostViews($cid);
    }

    $cookieKey = 'post_viewed_' . $cid;
    if (\Typecho\Cookie::get($cookieKey)) return getPostViews($cid);

    normalizeViewFields();
    $db = \Typecho\Db::get();
    $row = $db->fetchRow(
        $db->select('str_value')
            ->from('table.fields')
            ->where('cid = ?', $cid)
            ->where('name = ?', POST_VIEWS_FIELD)
            ->limit(1)
    );

    if ($row) {
        $views = (int) $row['str_value'] + 1;
        $db->query(
            $db->update('table.fields')
                ->rows(array('type' => 'str', 'str_value' => (string) $views, 'int_value' => 0))
                ->where('cid = ?', $cid)
                ->where('name = ?', POST_VIEWS_FIELD)
        );
    } else {
        $db->query(
            $db->insert('table.fields')->rows(array(
                'cid' => $cid,
                'name' => POST_VIEWS_FIELD,
                'type' => 'str',
                'str_value' => '1',
                'int_value' => 0,
                'float_value' => 0,
            ))
        );
        $views = 1;
    }

    \Typecho\Cookie::set($cookieKey, '1', $archive->options->time + 86400);
    return $views;
}

class PopularPosts extends \Widget\Base\Contents
{
    public function execute()
    {
        normalizeViewFields();
        $this->parameter->setDefault(array('pageSize' => 5));
        $select = $this->select('table.contents.*', 'table.fields.str_value AS postViews')
            ->join(
                'table.fields',
                "table.fields.cid = table.contents.cid AND table.fields.name = 'views'",
                \Typecho\Db::LEFT_JOIN
            )
            ->where('table.contents.password IS NULL OR table.contents.password = ?', '')
            ->where('table.contents.status = ?', 'publish')
            ->where('table.contents.created < ?', $this->options->time)
            ->where('table.contents.type = ?', 'post');

        $rows = $this->db->fetchAll($select);
        usort($rows, function (array $left, array $right): int {
            $viewOrder = (int) ($right['postViews'] ?? 0) <=> (int) ($left['postViews'] ?? 0);
            return $viewOrder !== 0 ? $viewOrder : (int) $right['created'] <=> (int) $left['created'];
        });

        foreach (array_slice($rows, 0, (int) $this->parameter->pageSize) as $row) {
            $this->push($row);
        }
    }
}

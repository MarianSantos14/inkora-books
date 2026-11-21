<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$pdo = db();
$stats = $pdo->query("SELECT COUNT(*) total,COALESCE(SUM(status='lido'),0) lidos,COALESCE(SUM(status='quero ler'),0) quero FROM estante")->fetch();
$topBooks = $pdo->query("SELECT l.id,l.titulo,l.autor,l.capa,COUNT(e.id) leituras FROM livro l JOIN estante e ON e.livro_id=l.id AND e.status='lido' GROUP BY l.id,l.titulo,l.autor,l.capa ORDER BY leituras DESC,l.titulo ASC LIMIT 10")->fetchAll();
$topAuthors = $pdo->query("SELECT l.autor,COUNT(e.id) leituras FROM livro l JOIN estante e ON e.livro_id=l.id AND e.status='lido' GROUP BY l.autor ORDER BY leituras DESC,l.autor ASC LIMIT 5")->fetchAll();
$topPublishers = $pdo->query("SELECT COALESCE(NULLIF(TRIM(l.editora),''),'Editora não informada') editora,COUNT(e.id) leituras FROM livro l JOIN estante e ON e.livro_id=l.id AND e.status='lido' GROUP BY COALESCE(NULLIF(TRIM(l.editora),''),'Editora não informada') ORDER BY leituras DESC,editora ASC LIMIT 3")->fetchAll();
$allBooks = $pdo->query('SELECT id,titulo,autor,editora,ano_publicacao,capa FROM livro ORDER BY titulo ASC')->fetchAll();
$chartColors = ['#3568a8', '#4f87c3', '#6e9b83', '#c57670', '#8b7db0'];
$chartPayload = [
    'books' => array_map(static fn($row) => ['title' => $row['titulo'], 'author' => $row['autor'], 'reads' => (int)$row['leituras'], 'cover' => !empty($row['capa']) ? image_url($row['capa'], '') : null, 'url' => url('livros/ver.php?id=') . (int)$row['id']], $topBooks),
    'authors' => array_map(static fn($row) => ['name' => $row['autor'], 'reads' => (int)$row['leituras']], $topAuthors),
    'publishers' => array_map(static fn($row) => ['name' => $row['editora'], 'reads' => (int)$row['leituras']], $topPublishers),
    'colors' => $chartColors,
];
$pageTitle = 'Painel de leitura';
require __DIR__ . '/../includes/header.php';
?>
<section class="page-heading">
    <div>
        <div class="eyebrow">VISÃO GERAL</div>
        <h1>Painel de leitura</h1>
        <p>Dados agregados da comunidade e do catálogo.</p>
    </div>
</section>
<div class="stat-grid admin-dashboard-stats">
    <div class="stat-card"><span>Livros na estante</span><b><?= number_format((int)$stats['total'], 0, ',', '.') ?></b></div>
    <div class="stat-card"><span>Leituras concluídas</span><b><?= number_format((int)$stats['lidos'], 0, ',', '.') ?></b></div>
    <div class="stat-card"><span>Na lista de desejos</span><b><?= number_format((int)$stats['quero'], 0, ',', '.') ?></b></div>
</div>

<section class="dashboard-chart-card dashboard-books-card" aria-labelledby="top-books-title">
    <div class="dashboard-chart-heading">
        <div>
            <div class="eyebrow">RANKING DE LEITURA</div>
            <h2 id="top-books-title">Livros mais lidos</h2>
            <p>Top 10 por leituras concluídas</p>
        </div><span class="chart-range-badge">TOP 10</span>
    </div>
    <?php if (!$topBooks): ?><div class="dashboard-chart-empty">Ainda não há leituras concluídas.</div><?php else: ?>
        <div class="dashboard-books-scroll" role="region" tabindex="0" aria-label="Gráfico e capas dos livros mais lidos">
            <div class="chart-canvas-wrap books-chart-wrap"><canvas id="booksChart" aria-label="Gráfico de barras dos dez livros mais lidos"></canvas></div>
            <div class="book-cover-list" style="--book-count:<?= count($topBooks) ?>"><?php foreach ($topBooks as $index => $book): ?><a class="book-cover-card" href="<?= url('livros/ver.php') ?>?id=<?= (int)$book['id'] ?>" title="<?= e($book['titulo'] . ' — ' . $book['autor']) ?>"><span class="book-cover-rank"><?= $index + 1 ?></span><span class="book-cover-image-wrap"><?php if (!empty($book['capa'])): ?><img src="<?= e(image_url($book['capa'], '')) ?>" alt="Capa de <?= e($book['titulo']) ?>" loading="lazy"><?php else: ?><span class="book-cover-placeholder">✦</span><?php endif; ?></span><strong><?= e($book['titulo']) ?></strong><small><?= e($book['autor']) ?></small></a><?php endforeach; ?></div>
        </div>
    <?php endif; ?>
</section>

<div class="dashboard-chart-grid">
    <section class="dashboard-chart-card" aria-labelledby="top-authors-title">
        <div class="dashboard-chart-heading">
            <div>
                <div class="eyebrow">POR AUTOR</div>
                <h2 id="top-authors-title">Autores mais lidos</h2>
                <p>Top 5 somando todos os livros</p>
            </div><span class="chart-range-badge">TOP 5</span>
        </div>
        <?php if (!$topAuthors): ?><div class="dashboard-chart-empty">Ainda não há leituras concluídas.</div><?php else: ?><div class="author-chart-layout">
                <div class="chart-canvas-wrap author-chart-wrap"><canvas id="authorsChart" aria-label="Gráfico de pizza dos cinco autores mais lidos"></canvas></div>
                <ul class="chart-legend" id="authorLegend"></ul>
            </div><?php endif; ?>
    </section>
    <section class="dashboard-chart-card" aria-labelledby="top-publishers-title">
        <div class="dashboard-chart-heading">
            <div>
                <div class="eyebrow">POR EDITORA</div>
                <h2 id="top-publishers-title">Editoras mais lidas</h2>
                <p>Top 3 por leituras concluídas</p>
            </div><span class="chart-range-badge">TOP 3</span>
        </div>
        <?php if (!$topPublishers): ?><div class="dashboard-chart-empty">Ainda não há leituras concluídas.</div><?php else: ?><div class="chart-canvas-wrap publishers-chart-wrap"><canvas id="publishersChart" aria-label="Gráfico de barras das três editoras mais lidas"></canvas></div><?php endif; ?>
    </section>
</div>
<section class="section-head dashboard-all-books-heading">
    <div>
        <div class="eyebrow">CATÁLOGO COMPLETO</div>
        <h2>Todos os livros</h2>
    </div><span class="status-pill"><?= number_format(count($allBooks), 0, ',', '.') ?> livros</span>
</section>
<?php if (!$allBooks): ?><div class="empty-state">
        <p>Nenhum livro cadastrado no catálogo.</p>
    </div><?php else: ?>
    <div class="table-wrap dashboard-all-books">
        <table>
            <thead>
                <tr>
                    <th>Capa</th>
                    <th>Livro</th>
                    <th>Autor</th>
                    <th>Editora</th>
                    <th class="dashboard-book-year">Ano</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody><?php foreach ($allBooks as $book): ?><tr>
                        <td class="book-cover-cell"><a href="<?= url('livros/ver.php') ?>?id=<?= (int)$book['id'] ?>"><?php $thumbnailBook = $book;
                                                                                                                        require __DIR__ . '/../includes/book_thumbnail.php'; ?></a></td>
                        <td><a class="text-link" href="<?= url('livros/ver.php') ?>?id=<?= (int)$book['id'] ?>"><?= e($book['titulo']) ?></a></td>
                        <td><?= e($book['autor']) ?></td>
                        <td><?= e($book['editora'] ?: '—') ?></td>
                        <td class="dashboard-book-year"><?= e((string)($book['ano_publicacao'] ?: '—')) ?></td>
                        <td>
                            <div class="manage-links dashboard-book-actions"><a href="<?= url('livros/ver.php') ?>?id=<?= (int)$book['id'] ?>">Ver</a><a href="<?= url('livros/form.php') ?>?id=<?= (int)$book['id'] ?>">Editar</a>
                                <form method="post" action="<?= url('pages/actions.php') ?>" data-confirm="Excluir este livro e sua capa local?"><input type="hidden" name="csrf" value="<?= e(csrf()) ?>"><input type="hidden" name="action" value="delete_book"><input type="hidden" name="id" value="<?= (int)$book['id'] ?>"><button type="submit">Excluir</button></form>
                            </div>
                        </td>
                    </tr><?php endforeach; ?></tbody>
        </table>
    </div>
<?php endif; ?>
<?php if ($topBooks || $topAuthors || $topPublishers): ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.8/dist/chart.umd.min.js"></script>
    <script>
        (() => {
            const data = <?= json_encode($chartPayload, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
            if (!window.Chart) return;
            const ink = getComputedStyle(document.documentElement).getPropertyValue('--ink').trim() || '#172b46';
            const muted = getComputedStyle(document.documentElement).getPropertyValue('--muted').trim() || '#7e8ba0';
            const line = getComputedStyle(document.documentElement).getPropertyValue('--line').trim() || '#e8edf4';
            const formatted = value => new Intl.NumberFormat('pt-BR').format(value);
            const countLabels = {
                id: 'countLabels',
                afterDatasetsDraw(chart) {
                    const {
                        ctx
                    } = chart;
                    ctx.save();
                    ctx.font = '600 14.4px DM Sans, sans-serif';
                    ctx.fillStyle = ink;
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'bottom';
                    chart.data.datasets.forEach((dataset, datasetIndex) => chart.getDatasetMeta(datasetIndex).data.forEach((bar, index) => {
                        const {
                            x,
                            y
                        } = bar.tooltipPosition();
                        ctx.fillText(formatted(dataset.data[index]), x, y - 8);
                    }));
                    ctx.restore();
                }
            };
            if (data.books.length) new Chart(document.getElementById('booksChart'), {
                type: 'bar',
                data: {
                    labels: data.books.map(book => book.title),
                    datasets: [{
                        data: data.books.map(book => book.reads),
                        backgroundColor: data.books.map((_, i) => i === 0 ? '#3568a8' : '#95b4d5'),
                        hoverBackgroundColor: '#4f87c3',
                        borderRadius: 8,
                        borderSkipped: false,
                        maxBarThickness: 54
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: {
                        padding: {
                            top: 24,
                            left: 4,
                            right: 4
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#172b46',
                            padding: 12,
                            cornerRadius: 8,
                            titleFont: {
                                family: 'DM Sans',
                                weight: '600'
                            },
                            bodyFont: {
                                family: 'DM Sans'
                            },
                            callbacks: {
                                title: items => data.books[items[0].dataIndex]?.title || '',
                                label: item => `${formatted(item.raw)} leituras`
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            border: {
                                display: false
                            },
                            ticks: {
                                display: false
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grace: '25%',
                            border: {
                                display: false
                            },
                            grid: {
                                color: line,
                                drawTicks: false
                            },
                            ticks: {
                                display: false,
                                precision: 0
                            }
                        }
                    }
                },
                plugins: [countLabels]
            });
            if (data.authors.length) {
                new Chart(document.getElementById('authorsChart'), {
                    type: 'doughnut',
                    data: {
                        labels: data.authors.map(a => a.name),
                        datasets: [{
                            data: data.authors.map(a => a.reads),
                            backgroundColor: data.colors,
                            borderColor: getComputedStyle(document.documentElement).getPropertyValue('--surface').trim() || '#fff',
                            borderWidth: 5,
                            hoverOffset: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '68%',
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: '#172b46',
                                padding: 12,
                                cornerRadius: 8,
                                callbacks: {
                                    label: item => `${item.label}: ${formatted(item.raw)} leituras (${Math.round(item.raw/data.authors.reduce((sum,a)=>sum+a.reads,0)*100)}%)`
                                }
                            }
                        }
                    }
                });
                const total = data.authors.reduce((sum, a) => sum + a.reads, 0);
                document.getElementById('authorLegend').innerHTML = data.authors.map((a, i) => `<li><span class="chart-legend-dot" style="background:${data.colors[i]}"></span><span class="chart-legend-name" title="${a.name}">${a.name}</span><strong>${formatted(a.reads)}</strong><small>${total?Math.round(a.reads/total*100):0}%</small></li>`).join('');
            }
            if (data.publishers.length) new Chart(document.getElementById('publishersChart'), {
                type: 'bar',
                data: {
                    labels: data.publishers.map(p => p.name),
                    datasets: [{
                        data: data.publishers.map(p => p.reads),
                        backgroundColor: data.colors.slice(0, 3),
                        borderRadius: 8,
                        borderSkipped: false,
                        barThickness: 28
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: {
                        padding: {
                            top: 4,
                            right: 34
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#172b46',
                            padding: 12,
                            cornerRadius: 8,
                            callbacks: {
                                label: item => `${formatted(item.raw)} leituras`
                            }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            grace: '20%',
                            border: {
                                display: false
                            },
                            grid: {
                                color: line,
                                drawTicks: false
                            },
                            ticks: {
                                color: muted,
                                precision: 0,
                                font: {
                                    family: 'DM Sans',
                                    size: 13
                                }
                            }
                        },
                        y: {
                            border: {
                                display: false
                            },
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: ink,
                                font: {
                                    family: 'DM Sans',
                                    size: 11,
                                    weight: '600'
                                }
                            }
                        }
                    }
                },
                plugins: [countLabels]
            });
        })();
    </script>
<?php endif; ?>
<?php require __DIR__ . '/../includes/footer.php'; ?>
<template>
    <Head title="Блог — SushiDex"/>
    <MainLayout>
        <main class="inner-page posts-page">
            <div class="shell posts-shell">
                <header class="page-intro"><p class="eyebrow dark"><span></span> Журнал SushiDex</p>
                    <h1>Истории о вкусе</h1>
                    <p>Рассказываем о японской кухне, продуктах и маленьких деталях, из которых складывается хороший
                        ужин.</p></header>
                <section class="post-list">
                    <article v-for="(post, index) in posts" :key="post.id" class="post-card">
                        <div class="post-number">{{ String(index + 1).padStart(2, '0') }}</div>
                        <div class="post-art" :class="`tone-${index % 3}`"><span>{{
                                post.category?.title || '食'
                            }}</span></div>
                        <div class="post-copy">
                            <div class="post-meta"><span>{{ post.category?.title || 'SushiDex' }}</span>
                                <time>{{ formatDate(post.created_at) }}</time>
                            </div>
                            <h2>{{ post.title }}</h2>
                            <p>{{ excerpt(post.content) }}</p>
                            <div class="post-footer"><span>Автор: {{
                                    post.user?.first_name || 'Команда SushiDex'
                                }}</span>
                                <Link :href="Posts.show(post.id).url">Читать статью <i>→</i></Link>
                            </div>
                        </div>
                    </article>
                </section>
                <button v-if="page < lastPage" type="button" class="load-more" @click="nextPage">Показать ещё
                    <span>＋</span></button>
            </div>
        </main>
    </MainLayout>
</template>
<script setup lang="ts">
import {Head, Link, router} from '@inertiajs/vue3';
import type {PostPublicResource} from '~gen/types/generated';
import Posts from '~routes/PostController';
import MainLayout from '~vue/Layouts/MainLayout.vue';

const {posts, page, lastPage} = defineProps<{ posts: PostPublicResource[]; page: number; lastPage: number }>();

function formatDate(date: string): string {
    return new Intl.DateTimeFormat('ru-RU', {day: 'numeric', month: 'long', year: 'numeric'}).format(new Date(date));
}

function excerpt(content: string | null): string {
    if (!content) return 'Новая история о японской кухне, свежих продуктах и культуре вкуса.';
    const plainText = content.replace(/<[^>]*>/g, '').trim();
    return plainText.length > 170 ? `${plainText.slice(0, 170)}…` : plainText;
}

function nextPage(): void {
    if (page < lastPage) router.visit(Posts.index({query: {page: page + 1}}), {only: ['posts', 'page']});
}
</script>
<style scoped>
.posts-shell {
    max-width: 1040px
}

.post-list {
    border-top: 1px solid #d9cec2
}

.post-card {
    display: grid;
    grid-template-columns:52px 280px 1fr;
    gap: 28px;
    padding: 34px 0;
    border-bottom: 1px solid #d9cec2
}

.post-number {
    padding-top: 7px;
    color: #ac9f94;
    font-family: 'Prata', serif;
    font-size: 12px
}

.post-art {
    min-height: 215px;
    display: grid;
    place-items: center;
    position: relative;
    overflow: hidden;
    background: #d47860
}

.post-art::before, .post-art::after {
    content: '';
    position: absolute;
    width: 145px;
    height: 145px;
    border: 1px solid rgba(255, 255, 255, .38);
    border-radius: 50%
}

.post-art::after {
    width: 105px;
    height: 105px;
    border-color: rgba(23, 23, 22, .12)
}

.post-art span {
    position: relative;
    z-index: 1;
    max-width: 80%;
    color: rgba(255, 255, 255, .9);
    font-family: serif;
    font-size: 31px;
    text-align: center
}

.post-art.tone-1 {
    background: #778967
}

.post-art.tone-2 {
    background: #b9895d
}

.post-copy {
    display: flex;
    flex-direction: column;
    padding: 5px 0
}

.post-meta {
    display: flex;
    gap: 16px;
    color: #94877c;
    font-size: 9px;
    font-weight: 700;
    letter-spacing: .12em;
    text-transform: uppercase
}

.post-meta span {
    color: #c15e48
}

.post-copy h2 {
    margin: 19px 0 13px;
    font-family: 'Prata', serif;
    font-size: clamp(23px, 3vw, 31px);
    font-weight: 400;
    line-height: 1.3
}

.post-copy > p {
    margin: 0;
    color: #786e65;
    font-size: 13px;
    line-height: 1.75
}

.post-footer {
    display: flex;
    align-items: end;
    justify-content: space-between;
    gap: 20px;
    margin-top: auto;
    padding-top: 18px
}

.post-footer > span {
    color: #9b8f84;
    font-size: 10px
}

.post-footer a {
    display: inline-flex;
    gap: 18px;
    padding-bottom: 5px;
    border-bottom: 1px solid #b7aaa0;
    color: #171716;
    font-size: 11px;
    font-weight: 800;
    text-decoration: none
}

.post-footer a:hover {
    color: #df5f45;
    border-color: #df5f45
}

.post-footer i {
    color: #df5f45;
    font-style: normal
}

.load-more {
    width: 100%;
    margin-top: 34px;
    padding: 18px 24px;
    display: flex;
    justify-content: center;
    gap: 18px;
    border: 1px solid #cfc3b7;
    background: transparent;
    color: #171716;
    font: inherit;
    font-size: 12px;
    font-weight: 800;
    cursor: pointer
}

@media (max-width: 760px) {
    .post-card {
        grid-template-columns:34px 1fr
    }

    .post-art {
        grid-column: 2;
        min-height: 190px
    }

    .post-copy {
        grid-column: 2
    }
}

@media (max-width: 500px) {
    .post-card {
        display: block
    }

    .post-number {
        margin-bottom: 12px
    }

    .post-art {
        margin-bottom: 22px
    }

    .post-footer {
        align-items: start;
        flex-direction: column
    }
}
</style>

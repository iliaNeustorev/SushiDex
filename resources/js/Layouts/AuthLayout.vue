<template>
    <MainLayout>
        <main class="auth-page">
            <div class="shell auth-shell">
                <section class="auth-story">
                    <Link :href="GeneralRoutes.index().url" class="auth-back">← На главную</Link>
                    <div class="auth-story-copy">
                        <p class="eyebrow"><span></span> SushiDex club</p>
                        <h1><slot name="title" /></h1>
                        <p><slot name="description" /></p>
                    </div>
                    <div class="auth-signature">
                        <span class="signature-mark">よ</span>
                        <p><strong>Свежесть в каждой детали</strong><small>Готовим только после вашего заказа</small></p>
                    </div>
                </section>

                <section class="auth-form-panel">
                    <div class="auth-form-wrap"><slot /></div>
                </section>
            </div>
        </main>
    </MainLayout>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import GeneralRoutes from '~routes/GeneralController';
import MainLayout from '~vue/Layouts/MainLayout.vue';
</script>

<style scoped>
.auth-page { min-height:calc(100vh - 180px); padding:48px 0 80px; background:#f5f0e8 }
.auth-shell { display:grid; grid-template-columns:minmax(360px,.9fr) minmax(440px,1.1fr); min-height:690px; padding:0; overflow:hidden; background:#fffaf4; box-shadow:0 24px 70px rgba(51,38,27,.09) }
.auth-story { position:relative; display:flex; flex-direction:column; justify-content:space-between; overflow:hidden; padding:45px 52px; background:#252522; color:#fff }
.auth-story::before,.auth-story::after { content:''; position:absolute; right:-120px; bottom:90px; width:360px; height:360px; border:1px solid rgba(240,145,114,.25); border-radius:50% }
.auth-story::after { right:-45px; bottom:165px; width:210px; height:210px; border-color:rgba(255,255,255,.11) }
.auth-back { position:relative; z-index:1; align-self:start; padding-bottom:5px; border-bottom:1px solid rgba(255,255,255,.25); color:rgba(255,255,255,.7); font-size:11px; font-weight:700; text-decoration:none }
.auth-story-copy { position:relative; z-index:1; max-width:430px }
.auth-story-copy h1 { margin:0; font-family:'Prata',serif; font-size:clamp(42px,5vw,64px); font-weight:400; line-height:1.12; letter-spacing:-.035em }
.auth-story-copy>p:last-child { margin:24px 0 0; color:rgba(255,255,255,.58); font-size:15px; line-height:1.8 }
.auth-signature { position:relative; z-index:1; display:flex; align-items:center; gap:15px }
.signature-mark { width:44px; height:44px; display:grid; place-items:center; border:1px solid #df5f45; border-radius:50%; color:#f09172; font-family:serif; font-size:20px }
.auth-signature p { display:flex; flex-direction:column; margin:0; font-size:11px }
.auth-signature small { margin-top:3px; color:rgba(255,255,255,.4); font-size:9px }
.auth-form-panel { display:grid; place-items:center; padding:64px clamp(34px,6vw,82px) }
.auth-form-wrap { width:100%; max-width:460px }
.auth-form-wrap :deep(.auth-kicker) { margin:0 0 10px; color:#bd654f; font-size:10px; font-weight:800; letter-spacing:.16em; text-transform:uppercase }
.auth-form-wrap :deep(.auth-heading) { margin:0; font-family:'Prata',serif; font-size:36px; font-weight:400 }
.auth-form-wrap :deep(.auth-lead) { margin:12px 0 34px; color:#887d73; font-size:13px; line-height:1.7 }
.auth-form-wrap :deep(.auth-form) { display:flex; flex-direction:column; gap:6px }
.auth-form-wrap :deep(.v-field) { border-radius:0; background:#fff }
.auth-form-wrap :deep(.v-field__outline) { color:#d4c8bc }
.auth-form-wrap :deep(.v-label) { color:#766b62; font-size:13px }
.auth-form-wrap :deep(.auth-submit) { min-height:54px; margin-top:10px; border-radius:0; background:#df5f45!important; color:#fff; font-weight:800; letter-spacing:0; text-transform:none }
.auth-form-wrap :deep(.auth-submit:hover) { background:#cf533b!important }
.auth-form-wrap :deep(.auth-row) { display:flex; align-items:center; justify-content:space-between; gap:20px }
.auth-form-wrap :deep(.auth-link) { color:#6f655d; font-size:11px; font-weight:700; text-decoration:none }
.auth-form-wrap :deep(.auth-link:hover) { color:#df5f45 }
.auth-form-wrap :deep(.auth-switch) { margin:28px 0 0; padding-top:24px; border-top:1px solid #e4dad0; color:#8b8076; font-size:12px; text-align:center }
.auth-form-wrap :deep(.auth-switch a) { margin-left:5px; color:#171716; font-weight:800; text-decoration:none }
@media(max-width:850px){.auth-page{padding:0}.auth-shell{width:100%;grid-template-columns:1fr;box-shadow:none}.auth-story{min-height:370px;padding:35px 32px}.auth-signature{display:none}.auth-form-panel{padding:52px 32px 70px}}
</style>

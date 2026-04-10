<script setup lang="ts">
import { ref, reactive } from 'vue'

const props = defineProps<{
  modelValue: boolean
}>()

const emit = defineEmits<{
  (e: 'update:modelValue', value: boolean): void
  (e: 'submit'): void
}>()

function closeModal() {
  emit('update:modelValue', false)
}

const formSent = ref(false)
const form = reactive({ name: '', company: '', phone: '' })

function submitForm() {
  formSent.value = true
  emit('submit')
}
</script>

<template>
  <!-- ═══════════════════════════════════════════════════════
           МОДАЛЬНОЕ ОКНО — Форма регистрации
      ═══════════════════════════════════════════════════════════ -->
  <Transition name="modal">
    <div v-if="modelValue" class="gl-modal-overlay" @click.self="closeModal">
      <div class="gl-modal">
        <button class="gl-modal-close" @click="closeModal" aria-label="Закрыть">✕</button>

        <!-- Успех -->
        <Transition name="fade" mode="out-in">
          <div v-if="formSent" class="gl-modal-success">
            <div class="gl-modal-success-icon">
              <svg viewBox="0 0 24 24" fill="none" width="32" height="32">
                <path d="M20 6L9 17l-5-5" stroke="#93c5fd" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </div>
            <h3 class="gl-modal-success-title">Заявка отправлена!</h3>
            <p class="gl-modal-success-text">Мы свяжемся с вами в ближайшее время и расскажем о форматах участия в календаре.</p>
            <button class="gl-modal-success-btn" @click="closeModal">Закрыть</button>
          </div>

          <!-- Форма -->
          <div v-else class="gl-modal-body">
            <div class="gl-modal-head">
              <div class="gl-section-line" />
              <h2 class="gl-modal-title">Хочу в календарь</h2>
              <p class="gl-modal-sub">Оставьте контакты — мы подберём подходящий формат участия и свяжемся с вами</p>
            </div>

            <div class="gl-modal-form">
              <div class="gl-field">
                <label class="gl-field-label">Имя и фамилия *</label>
                <input
                  v-model="form.name"
                  type="text"
                  class="gl-field-input"
                  placeholder="Иванов Иван"
                  autocomplete="name"
                />
              </div>
              <div class="gl-field">
                <label class="gl-field-label">Компания / организация *</label>
                <input
                  v-model="form.company"
                  type="text"
                  class="gl-field-input"
                  placeholder="ООО «Название»"
                  autocomplete="organization"
                />
              </div>
              <div class="gl-field">
                <label class="gl-field-label">Телефон *</label>
                <input
                  v-model="form.phone"
                  type="tel"
                  class="gl-field-input"
                  placeholder="+7 (___) ___-__-__"
                  autocomplete="tel"
                />
              </div>

              <button
                class="gl-modal-submit"
                :disabled="!form.name || !form.company || !form.phone"
                @click="submitForm"
              >
                Отправить заявку
                <svg viewBox="0 0 16 16" fill="none" width="13" height="13">
                  <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
              </button>

              <p class="gl-modal-hint">
                Нажимая «Отправить», вы соглашаетесь с
                <a href="#" class="gl-modal-link">политикой конфиденциальности</a>
              </p>
            </div>
          </div>
        </Transition>
      </div>
    </div>
  </Transition>
</template>

<style scoped>
/* ═══════════════════════════════════════
   МОДАЛЬНОЕ ОКНО
═══════════════════════════════════════ */
.gl-modal-overlay {
  position: fixed; inset: 0; z-index: 200;
  background: rgba(3,5,16,0.88);
  backdrop-filter: blur(14px);
  display: flex; align-items: center; justify-content: center;
  padding: 24px;
}
.gl-modal {
  position: relative;
  width: 100%; max-width: 480px;
  background: linear-gradient(150deg, #0d1530 0%, #091220 100%);
  border: 1px solid rgba(96,165,250,0.18);
  border-radius: 20px;
  padding: 48px;
  box-shadow:
    0 0 0 1px rgba(255,255,255,0.03),
    0 40px 100px rgba(0,0,0,0.7),
    inset 0 1px 0 rgba(147,197,253,0.08);
  overflow: hidden;
}
.gl-modal::before {
  content: '';
  position: absolute; top: -80px; left: 50%; transform: translateX(-50%);
  width: 320px; height: 200px;
  background: radial-gradient(ellipse, rgba(59,130,246,0.14), transparent 70%);
  pointer-events: none;
}
.gl-modal-close {
  position: absolute; top: 18px; right: 20px;
  background: rgba(13,21,48,0.6); border: 1px solid rgba(96,165,250,0.12);
  color: #3d5a8a; font-size: 14px; width: 34px; height: 34px;
  border-radius: 9px; cursor: pointer; transition: all 0.2s;
  display: flex; align-items: center; justify-content: center;
}
.gl-modal-close:hover { color: #dce8f5; border-color: rgba(96,165,250,0.3); background: rgba(13,21,48,0.9); }

/* Заголовок */
.gl-modal-head { display: flex; flex-direction: column; gap: 10px; margin-bottom: 36px; }
.gl-modal-title {
  font-family: 'Cormorant Garamond', serif;
  font-size: 38px; font-weight: 600; line-height: 1.1;
  color: #e2edf8; letter-spacing: -0.01em; margin: 0;
}
.gl-modal-sub { font-size: 13px; line-height: 1.6; color: #3d5a8a; font-weight: 300; margin: 0; }

/* Форма */
.gl-modal-form { display: flex; flex-direction: column; gap: 16px; }
.gl-field { display: flex; flex-direction: column; gap: 6px; }
.gl-field-label {
  font-size: 10px; letter-spacing: 0.1em; text-transform: uppercase;
  color: #2e4a73; font-weight: 400;
}
.gl-field-input {
  width: 100%;
  background: rgba(10,16,40,0.6);
  border: 1px solid rgba(96,165,250,0.15);
  border-radius: 10px;
  padding: 13px 16px;
  font-family: 'DM Sans', sans-serif;
  font-size: 14px; font-weight: 400;
  color: #dce8f5;
  outline: none;
  transition: border-color 0.2s, background 0.2s, box-shadow 0.2s;
}
.gl-field-input::placeholder { color: #2a3f65; }
.gl-field-input:focus {
  border-color: rgba(147,197,253,0.4);
  background: rgba(10,16,40,0.85);
  box-shadow: 0 0 0 3px rgba(59,130,246,0.1);
}

.gl-modal-submit {
  width: 100%;
  display: flex; align-items: center; justify-content: center; gap: 8px;
  margin-top: 8px;
  padding: 14px 28px;
  font-family: 'DM Sans', sans-serif;
  font-size: 12px; letter-spacing: 0.1em; text-transform: uppercase;
  font-weight: 500;
  color: #06091a;
  background: #93c5fd;
  border: none; border-radius: 10px;
  cursor: pointer;
  transition: background 0.2s, transform 0.15s, opacity 0.2s;
}
.gl-modal-submit:hover:not(:disabled) { background: #bfdbfe; transform: translateY(-1px); }
.gl-modal-submit:disabled { opacity: 0.35; cursor: not-allowed; }

.gl-modal-hint {
  font-size: 11px; color: #2a3f65; text-align: center;
  margin: 0; line-height: 1.5;
}
.gl-modal-link { color: #3d5a8a; text-decoration: none; transition: color 0.2s; }
.gl-modal-link:hover { color: #93c5fd; }

/* Успех */
.gl-modal-success {
  display: flex; flex-direction: column; align-items: center;
  gap: 16px; text-align: center; padding: 16px 0;
}
.gl-modal-success-icon {
  width: 64px; height: 64px;
  border-radius: 50%;
  background: rgba(147,197,253,0.1);
  border: 1px solid rgba(147,197,253,0.25);
  display: flex; align-items: center; justify-content: center;
  animation: pop 0.4s cubic-bezier(0.16,1,0.3,1) both;
}
@keyframes pop { from { transform: scale(0.6); opacity: 0; } to { transform: scale(1); opacity: 1; } }
.gl-modal-success-title {
  font-family: 'Cormorant Garamond', serif;
  font-size: 32px; font-weight: 600;
  color: #e2edf8; margin: 0;
}
.gl-modal-success-text { font-size: 14px; line-height: 1.65; color: #3d5a8a; margin: 0; max-width: 320px; }
.gl-modal-success-btn {
  margin-top: 8px;
  padding: 11px 28px;
  font-family: 'DM Sans', sans-serif;
  font-size: 12px; letter-spacing: 0.08em; text-transform: uppercase;
  color: #4a6fa5;
  background: transparent;
  border: 1px solid rgba(96,165,250,0.2);
  border-radius: 9px; cursor: pointer;
  transition: all 0.2s;
}
.gl-modal-success-btn:hover { color: #93c5fd; border-color: rgba(147,197,253,0.4); background: rgba(147,197,253,0.05); }

/* Анимации модалки */
.modal-enter-active .gl-modal { animation: modal-in 0.35s cubic-bezier(0.16,1,0.3,1) both; }
.modal-leave-active .gl-modal { animation: modal-out 0.2s ease-in both; }
@keyframes modal-in  { from { opacity: 0; transform: translateY(24px) scale(0.97); } to { opacity: 1; transform: translateY(0) scale(1); } }
@keyframes modal-out { from { opacity: 1; transform: translateY(0) scale(1); } to { opacity: 0; transform: translateY(12px) scale(0.98); } }

</style>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { Http } from '@/Infrastructure'
import { useAuth } from '@/Modules/Auth'
import { CardTitle, GCard } from '@/Shared'
import type { CustomerDocumentDTO } from '@/Modules/Customer'

const props = defineProps<{ customerId: string }>()

const { hasRole } = useAuth()
const isAdmin = hasRole('admin')

const documents = ref<CustomerDocumentDTO[]>([])
const activeTab = ref<'person_photo' | 'identity_document' | 'house_photo'>('person_photo')
const uploading = ref(false)
const deleting = ref(false)

const typeLabel: Record<string, string> = {
  person_photo: 'Foto de la persona',
  identity_document: 'Documento de identidad',
  house_photo: 'Foto de la casa',
}

const typeIcon: Record<string, string> = {
  person_photo: '👤',
  identity_document: '🪪',
  house_photo: '🏠',
}

const currentDocs = computed(() =>
  documents.value.filter(d => d.type === activeTab.value)
)

const hasDocument = computed(() => currentDocs.value.length > 0)

const frontDoc = computed(() =>
  documents.value.find(d => d.type === 'identity_document' && d.side === 'front')
)

const backDoc = computed(() =>
  documents.value.find(d => d.type === 'identity_document' && d.side === 'back')
)

const BASE = '/customers'

const loadDocuments = async () => {
  try {
    documents.value = await Http.get<CustomerDocumentDTO[]>(`${BASE}/${props.customerId}/documents`)
  } catch {
    // silently fail
  }
}

const handleUpload = async (event: Event, side?: 'front' | 'back') => {
  const input = event.target as HTMLInputElement
  if (!input.files?.length) return

  const file = input.files[0]
  uploading.value = true

  try {
    const formData = new FormData()
    formData.append('file', file)
    formData.append('type', activeTab.value)
    if (side) {
      formData.append('side', side)
    }

    await Http.post(`${BASE}/${props.customerId}/documents`, formData)
    await loadDocuments()
    input.value = ''
  } catch (e: any) {
    alert('Error al subir documento: ' + (e.message || 'desconocido'))
  } finally {
    uploading.value = false
  }
}

const handleDelete = async (documentId: string) => {
  if (!confirm('¿Eliminar este documento?')) return
  deleting.value = true
  try {
    await Http.delete(`${BASE}/${props.customerId}/documents/${documentId}`)
    await loadDocuments()
  } catch {
    alert('Error al eliminar documento')
  } finally {
    deleting.value = false
  }
}

const formatFileSize = (bytes: number): string => {
  if (bytes < 1024) return bytes + ' B'
  if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB'
  return (bytes / 1048576).toFixed(1) + ' MB'
}

onMounted(loadDocuments)
</script>

<template>
  <GCard class="customer-documents">
    <CardTitle>Documentos</CardTitle>

    <div class="doc-tabs">
      <button
        v-for="type in (['person_photo', 'identity_document', 'house_photo'] as const)"
        :key="type"
        class="doc-tab"
        :class="{ active: activeTab === type }"
        @click="activeTab = type"
      >
        <span class="doc-tab__icon">{{ typeIcon[type] }}</span>
        <span class="doc-tab__label">{{ typeLabel[type] }}</span>
      </button>
    </div>

    <div class="doc-content">
      <!-- Identity document: show front + back slots -->
      <template v-if="activeTab === 'identity_document'">
        <div class="doc-side-section">
          <span class="doc-side-label">Frente</span>
          <div v-if="frontDoc" class="doc-item">
            <div class="doc-thumb">
              <img v-if="frontDoc.mimeType.startsWith('image/')" :src="frontDoc.url" :alt="frontDoc.originalName" />
              <div v-else class="doc-pdf-icon">PDF</div>
            </div>
            <div class="doc-meta">
              <span class="doc-name">{{ frontDoc.originalName }}</span>
              <span class="doc-size">{{ formatFileSize(frontDoc.size) }}</span>
            </div>
            <button
              v-if="isAdmin"
              class="doc-delete"
              :disabled="deleting"
              @click="handleDelete(frontDoc.id)"
            >
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="3 6 5 6 21 6" /><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
              </svg>
            </button>
          </div>
          <div v-else class="doc-empty">
            <p>Sin foto del frente</p>
          </div>
          <div v-if="isAdmin" class="doc-upload">
            <label class="doc-upload-btn" :class="{ loading: uploading }">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" /><polyline points="17 8 12 3 7 8" /><line x1="12" y1="3" x2="12" y2="15" />
              </svg>
              {{ uploading ? 'Subiendo...' : frontDoc ? 'Reemplazar' : 'Subir foto' }}
              <input
                type="file"
                accept="image/jpeg,image/png,image/webp,application/pdf"
                capture="environment"
                @change="(e) => handleUpload(e, 'front')"
              />
            </label>
          </div>
        </div>

        <div class="doc-side-section">
          <span class="doc-side-label">Reverso</span>
          <div v-if="backDoc" class="doc-item">
            <div class="doc-thumb">
              <img v-if="backDoc.mimeType.startsWith('image/')" :src="backDoc.url" :alt="backDoc.originalName" />
              <div v-else class="doc-pdf-icon">PDF</div>
            </div>
            <div class="doc-meta">
              <span class="doc-name">{{ backDoc.originalName }}</span>
              <span class="doc-size">{{ formatFileSize(backDoc.size) }}</span>
            </div>
            <button
              v-if="isAdmin"
              class="doc-delete"
              :disabled="deleting"
              @click="handleDelete(backDoc.id)"
            >
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="3 6 5 6 21 6" /><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
              </svg>
            </button>
          </div>
          <div v-else class="doc-empty">
            <p>Sin foto del reverso</p>
          </div>
          <div v-if="isAdmin" class="doc-upload">
            <label class="doc-upload-btn" :class="{ loading: uploading }">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" /><polyline points="17 8 12 3 7 8" /><line x1="12" y1="3" x2="12" y2="15" />
              </svg>
              {{ uploading ? 'Subiendo...' : backDoc ? 'Reemplazar' : 'Subir foto' }}
              <input
                type="file"
                accept="image/jpeg,image/png,image/webp,application/pdf"
                capture="environment"
                @change="(e) => handleUpload(e, 'back')"
              />
            </label>
          </div>
        </div>
      </template>

      <!-- Other types: simple single-document layout -->
      <template v-else>
        <div v-if="hasDocument" class="doc-preview">
          <div v-for="doc in currentDocs" :key="doc.id" class="doc-item">
            <div class="doc-thumb">
              <img v-if="doc.mimeType.startsWith('image/')" :src="doc.url" :alt="doc.originalName" />
              <div v-else class="doc-pdf-icon">PDF</div>
            </div>
            <div class="doc-meta">
              <span class="doc-name">{{ doc.originalName }}</span>
              <span class="doc-size">{{ formatFileSize(doc.size) }}</span>
            </div>
            <button
              v-if="isAdmin"
              class="doc-delete"
              :disabled="deleting"
              @click="handleDelete(doc.id)"
            >
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="3 6 5 6 21 6" /><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
              </svg>
            </button>
          </div>
        </div>

        <div v-else class="doc-empty">
          <p>No hay documento subido</p>
        </div>

        <div v-if="isAdmin" class="doc-upload">
          <label class="doc-upload-btn" :class="{ loading: uploading }">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" /><polyline points="17 8 12 3 7 8" /><line x1="12" y1="3" x2="12" y2="15" />
            </svg>
            {{ uploading ? 'Subiendo...' : hasDocument ? 'Reemplazar' : 'Subir archivo' }}
            <input
              type="file"
              accept="image/jpeg,image/png,image/webp,application/pdf"
              capture="environment"
              @change="(e) => handleUpload(e)"
            />
          </label>
        </div>
      </template>

      <p v-if="!isAdmin" class="doc-readonly">
        Solo lectura — Solo los administradores pueden subir documentos
      </p>
    </div>
  </GCard>
</template>

<style scoped>
.customer-documents {
  margin-top: 1rem;
}

.doc-tabs {
  display: flex;
  gap: 4px;
  margin-top: 10px;
  border-bottom: 1px solid rgba(212, 175, 55, 0.2);
}

.doc-tab {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  padding: 8px 12px;
  background: transparent;
  border: none;
  border-bottom: 2px solid transparent;
  color: #94a3b8;
  cursor: pointer;
  font-size: 12px;
  font-weight: 600;
  transition: all 0.15s;
  touch-action: manipulation;
  min-height: 44px;
}

.doc-tab:hover {
  color: #e0e0e0;
}

.doc-tab.active {
  color: #d4af37;
  border-bottom-color: #d4af37;
}

.doc-content {
  padding: 12px 0;
}

.doc-preview {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.doc-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 8px;
  background: rgba(255, 255, 255, 0.03);
  border: 1px solid rgba(212, 175, 55, 0.15);
  border-radius: 8px;
}

.doc-thumb {
  width: 56px;
  height: 56px;
  border-radius: 6px;
  overflow: hidden;
  flex-shrink: 0;
  background: #0f3460;
  display: flex;
  align-items: center;
  justify-content: center;
}

.doc-thumb img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.doc-pdf-icon {
  font-size: 11px;
  font-weight: 700;
  color: #d4af37;
}

.doc-meta {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 2px;
  min-width: 0;
}

.doc-name {
  font-size: 13px;
  color: #e0e0e0;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.doc-size {
  font-size: 11px;
  color: #94a3b8;
}

.doc-delete {
  background: transparent;
  border: none;
  color: #ef4444;
  cursor: pointer;
  padding: 8px;
  border-radius: 6px;
  transition: background 0.15s;
  touch-action: manipulation;
  min-height: 44px;
  min-width: 44px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.doc-delete:hover {
  background: rgba(239, 68, 68, 0.15);
}

.doc-delete:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}

.doc-side-section {
  margin-bottom: 16px;
}

.doc-side-label {
  display: block;
  font-size: 12px;
  font-weight: 700;
  color: #d4af37;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 8px;
}

.doc-empty {
  text-align: center;
  padding: 20px;
  color: #94a3b8;
  font-size: 13px;
}

.doc-upload {
  margin-top: 12px;
}

.doc-upload-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  width: 100%;
  padding: 12px;
  min-height: 44px;
  background: #0f3460;
  border: 1px dashed rgba(212, 175, 55, 0.4);
  border-radius: 8px;
  color: #d4af37;
  cursor: pointer;
  font-size: 14px;
  font-weight: 600;
  transition: all 0.15s;
  touch-action: manipulation;
}

.doc-upload-btn:hover {
  background: #1a1a5e;
}

.doc-upload-btn.loading {
  opacity: 0.6;
  pointer-events: none;
}

.doc-upload-btn input {
  display: none;
}

.doc-readonly {
  text-align: center;
  padding: 12px;
  color: #94a3b8;
  font-size: 12px;
  font-style: italic;
}
</style>

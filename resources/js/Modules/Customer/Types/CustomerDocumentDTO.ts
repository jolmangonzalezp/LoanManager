export interface CustomerDocumentDTO {
  id: string
  type: 'person_photo' | 'identity_document' | 'house_photo'
  side: 'front' | 'back' | null
  originalName: string
  url: string
  mimeType: string
  size: number
  createdAt: string
}

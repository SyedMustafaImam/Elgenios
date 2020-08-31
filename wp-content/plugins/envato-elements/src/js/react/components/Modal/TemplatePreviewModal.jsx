import React from 'react'
import TemplateModalWrapper from './TemplateModalWrapper'
import styles from './TemplatePreviewModal.module.scss'

const TemplatePreviewModal = ({ onCloseCallback, templateId, templateKitId, existingImports, templateScreenShotUrl, templatePreviewTitle }) => {
  return (
    <TemplateModalWrapper templateId={templateId} templateKitId={templateKitId} existingImports={existingImports} templatePreviewTitle={templatePreviewTitle} isOpen onCloseCallback={onCloseCallback}>
      <img className={styles.previewTemplate} src={templateScreenShotUrl} alt={templatePreviewTitle} />
    </TemplateModalWrapper>
  )
}

export default TemplatePreviewModal

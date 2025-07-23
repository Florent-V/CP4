import { Controller } from "@hotwired/stimulus"

export default class extends Controller {
    static targets = ["tile", "result", "resetBtn", "codeDisplay", "codeInfo", "qrDisplay", "qrInfo", "emailResult"]
    static values = {
        splitterId: String,
        entityType: String,
        entityId: String,
        qrUrl: String,
        emailUrl: String,
        splitterName: String
    }

    connect()
    {
        this.currentShareData = {}
        // Cache des icônes pour réutilisation
        this.iconCache = this.cacheIcons()
    }

    /**
     * Met en cache les icônes UX disponibles pour utilisation en JavaScript
     */
    cacheIcons()
    {
        const iconTemplates = document.getElementById('icon-templates')
        const cache = {}

        if (iconTemplates) {
            const icons = iconTemplates.querySelectorAll('[data-icon-name]')
            icons.forEach(icon => {
                const iconName = icon.dataset.iconName
                cache[iconName] = icon.cloneNode(true)
            })
        }

        return cache
    }

    /**
     * Récupère une icône depuis le cache
     */
    getIcon(iconName)
    {
        if (this.iconCache[iconName]) {
            return this.iconCache[iconName].cloneNode(true)
        }
        return null
    }

    // Gestion des clics sur les tuiles
    selectTile(event)
    {
        const tile = event.currentTarget
        const shareType = tile.dataset.shareType

        // Réinitialiser les états
        this.resetAllTiles()
        this.hideAllResults()

        // Activer la tuile sélectionnée
        tile.classList.add('active', 'loading')

        // Traitement selon le type
        switch (shareType) {
            case 'email':
                this.handleEmailShare(tile)
                break
            case 'code':
                this.handleCodeShare(tile)
                break
            case 'qr':
                this.handleQRShare(tile)
                break
        }
    }

    // Gestion du partage par code (6 chiffres)
    async handleCodeShare(tile)
    {
        try {
            const response = await fetch(`/api/share/code/${this.entityTypeValue}/${this.entityIdValue}`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })

            const data = await response.json()

            if (data.success) {
                this.currentShareData = {
                    type: 'code',
                    code: data.code,
                    expiresAt: data.expiresAt,
                    entityDisplayName: data.entityDisplayName
                }

                this.showCodeResult(data)
            } else {
                throw new Error(data.message || 'Erreur lors de la génération du code')
            }
        } catch (error) {
            console.error('Erreur code:', error)
            this.showError('Erreur lors de la génération du code')
        } finally {
            this.stopLoading()
        }
    }

    // Gestion du partage par email (maintenant génère un lien sécurisé)
    async handleEmailShare(tile)
    {
        try {
            const response = await fetch(`/api/share/link/${this.entityTypeValue}/${this.entityIdValue}`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })

            const data = await response.json()

            if (data.success) {
                // Stocker les données du lien sécurisé
                this.currentShareData = {
                    type: 'email',
                    shareUrl: data.shareUrl,
                    token: data.token,
                    expiresAt: data.expiresAt,
                    entityDisplayName: data.entityDisplayName
                }

                this.showEmailResult(data)
            } else {
                throw new Error(data.message || 'Erreur lors de la génération du lien')
            }
        } catch (error) {
            console.error('Erreur email:', error)
            this.showError('Erreur lors de la génération du lien sécurisé')
        } finally {
            this.stopLoading()
        }
    }

    // Gestion du partage par QR (utilise le lien sécurisé)
    async handleQRShare(tile)
    {
        try {
            // D'abord générer le lien sécurisé
            const linkResponse = await fetch(`/api/share/link/${this.entityTypeValue}/${this.entityIdValue}`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })

            const linkData = await linkResponse.json()

            if (!linkData.success) {
                throw new Error(linkData.message || 'Erreur lors de la génération du lien')
            }

            // Puis générer le QR code
            const qrResponse = await fetch(this.qrUrlValue, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })

            const qrData = await qrResponse.json()

            if (qrData.success) {
                this.currentShareData = {
                    type: 'qr',
                    qrImage: qrData.qrImage,
                    shareUrl: linkData.shareUrl,
                    expiresAt: linkData.expiresAt,
                    entityDisplayName: linkData.entityDisplayName
                }

                this.showQRResult(qrData, linkData.shareUrl)
            } else {
                throw new Error(qrData.message || 'Erreur lors de la génération du QR code')
            }
        } catch (error) {
            console.error('Erreur QR:', error)
            this.showError('Erreur lors de la génération du QR code')
        } finally {
            this.stopLoading()
        }
    }

    // Afficher le résultat du code
    showCodeResult(data)
    {
        this.codeDisplayTarget.textContent = data.code
        this.codeInfoTarget.innerHTML = `
            <strong>${data.entityDisplayName}</strong><br>
            Expire le ${data.expiresAt}
        `
        this.showResult('code')
    }

    // Afficher le résultat du QR
    showQRResult(data, shareUrl)
    {
        this.qrDisplayTarget.innerHTML = `<img src="${data.qrImage}" alt="QR Code" class="img-fluid">`
        this.qrInfoTarget.innerHTML = `
            Scannez avec votre téléphone
        `
        this.showResult('qr')
    }

    // Afficher le résultat email
    showEmailResult(data)
    {
        const fullUrl = this.ensureAbsoluteUrl(data.shareUrl)

        this.emailResultTarget.innerHTML = `
            <div class="share-link-container">
                <div class="share-link-section mt-3">
                    <div class="share-link-display">
                        <a href="${fullUrl}" target="_blank" class="share-link">${fullUrl}</a>
                    </div>
                </div>
                <div class="share-meta mt-3">
                    <small class="text-muted">
                        ${this.getIcon('clock')?.outerHTML || '<i class="fas fa-clock me-1"></i>'}
                        Expire le ${data.expiresAt}
                    </small>
                </div>
            </div>
        `
        this.showResult('email')
    }

    // Afficher la zone de résultat
    showResult(type)
    {
        this.resultTarget.classList.add('active')

        // Masquer tous les résultats
        const allResults = this.element.querySelectorAll('.share-result-content')
        allResults.forEach(el => el.style.display = 'none')

        // Afficher le bon résultat
        const resultElement = this.element.querySelector(`#${type}-result`)
        if (resultElement) {
            resultElement.style.display = 'block'
        }

        this.resetBtnTarget.style.display = 'inline-block'
    }

    // S'assure qu'une URL est absolue
    ensureAbsoluteUrl(url) {
        if (!url) return url

        // Si l'URL est déjà absolue, la retourner telle quelle
        if (url.startsWith('http://') || url.startsWith('https://')) {
            return url
        }

        // Sinon, construire l'URL absolue
        const baseUrl = `${window.location.protocol}//${window.location.host}`
        return url.startsWith('/') ? baseUrl + url : baseUrl + '/' + url
    }

    // Copier le contenu
    copy(event)
    {
        const button = event.currentTarget
        const target = button.dataset.copyTarget
        let textToCopy = ''

        switch (target) {
            case 'code':
                textToCopy = this.currentShareData.code
                break
            case 'url':
                textToCopy = this.ensureAbsoluteUrl(this.currentShareData.shareUrl)
                break
        }

        if (textToCopy) {
            this.copyToClipboard(textToCopy, button)
        }
    }

    // Fonction de copie avec feedback
    async copyToClipboard(text, button)
    {
        try {
            await navigator.clipboard.writeText(text)

            // Animation de feedback avec icône UX
            const originalContent = button.innerHTML
            const checkIcon = this.getIcon('check-success')

            if (checkIcon) {
                button.innerHTML = checkIcon.outerHTML + ' Copié !'
            } else {
                button.innerHTML = '✓ Copié !'
            }

            button.classList.add('copied')

            setTimeout(() => {
                button.innerHTML = originalContent
                button.classList.remove('copied')
            }, 2000)
        } catch (err) {
            console.error('Erreur de copie:', err)
            alert('❌ Erreur lors de la copie')
        }
    }

    // Télécharger le QR code
    downloadQr()
    {
        if (this.currentShareData.qrImage) {
            const link = document.createElement('a')
            link.download = `qrcode-${this.splitterNameValue.replace(/\s+/g, '-')}.png`
            link.href = this.currentShareData.qrImage
            link.click()
        }
    }

    // Réinitialiser l'interface
    reset()
    {
        this.resetAllTiles()
        this.hideAllResults()
        this.currentShareData = {}
    }

    // Fonctions utilitaires
    resetAllTiles()
    {
        this.tileTargets.forEach(tile => {
            tile.classList.remove('active', 'loading')
        })
    }

    hideAllResults()
    {
        this.resultTarget.classList.remove('active')
        const allResults = this.element.querySelectorAll('.share-result-content')
        allResults.forEach(el => el.style.display = 'none')
        this.resetBtnTarget.style.display = 'none'
    }

    stopLoading()
    {
        this.tileTargets.forEach(tile => {
            tile.classList.remove('loading')
        })
    }

    showError(message)
    {
        alert('❌ ' + message)
        this.resetAllTiles()
    }
}

# 📊 Analisi Codebase - Metodi di Pagamento GestPay per WooCommerce

## 🏗️ Architettura Generale

Il plugin GestPay per WooCommerce implementa un sistema modulare di metodi di pagamento basato su:
- **Classe principale**: `WC_Gateway_Gestpay` (file principale)
- **Metodi specializzati**: Classi separate per ogni tipo di pagamento
- **Sistema di registrazione**: Filtro `woocommerce_payment_gateways` per aggiungere i gateway

## 💳 Metodi di Pagamento Supportati

### 1. **Carte di Credito (Base)**
- **Classe**: `WC_Gateway_Gestpay`
- **Tipo**: Generico per tutte le carte supportate
- **Caratteristiche**:
  - Supporto per Visa, Mastercard, American Express, JCB, Maestro
  - Salvataggio token per pagamenti ricorrenti
  - Gestione 3D Secure
  - Supporto per abbonamenti WooCommerce

### 2. **MyBank** 🏦
- **Classe**: `WC_Gateway_Gestpay_MYBANK`
- **File**: `inc/payment_types/gestpay-mybank.php`
- **Caratteristiche**:
  - Selezione banca obbligatoria su mobile
  - Lista banche dinamica tramite API SOAP
  - Filtro di ricerca case-insensitive
  - Logo personalizzato dopo il pagamento
  - JavaScript per gestione UI avanzata

### 3. **PayPal** 💰
- **Classe**: `WC_Gateway_Gestpay_PAYPAL`
- **File**: `inc/payment_types/gestpay-paypal.php`
- **Caratteristiche**:
  - Supporto abbonamenti WooCommerce
  - Descrizione accordo di fatturazione
  - Integrazione con WooCommerce Subscriptions

### 4. **PayPal Buy Now Pay Later** 💳
- **Classe**: `WC_Gateway_Gestpay_PAYPAL_BNPL`
- **File**: `inc/payment_types/gestpay-paypal_bnpl.php`
- **Caratteristiche**:
  - Pagamento rateizzato PayPal
  - Icona SVG personalizzata
  - Supporto abbonamenti

### 5. **Consel** 🏢
- **Classe**: `WC_Gateway_Gestpay_CONSEL`
- **File**: `inc/payment_types/gestpay-consel.php`
- **Caratteristiche**:
  - Parametri specifici per merchant ID
  - Informazioni cliente obbligatorie
  - Codice convenzione merchant

### 6. **Metodi Semplici** ⚡
- **BancomatPay**: `WC_Gateway_Gestpay_BANCOMATPAY`
- **BON**: `WC_Gateway_Gestpay_BON`
- **Compass**: `WC_Gateway_Gestpay_COMPASS`
- **MasterPass**: `WC_Gateway_Gestpay_MASTERPASS`

## 🔧 Sistema di Registrazione

### File: `inc/gestpay-pro-payment-types.php`
```php
$payment_types = array(
    'paypal',
    'paypal_bnpl', 
    'mybank',
    'consel',
    'masterpass',
    'compass',
    'bancomatpay',
);
```

**Processo di registrazione**:
1. Controllo se i metodi Pro sono abilitati
2. Caricamento dinamico delle classi
3. Aggiunta al filtro `woocommerce_payment_gateways`

## 🌍 Valute Supportate

### File: `inc/gestpay-currencies.php`
**32 valute supportate** con configurazioni specifiche:
- **EUR**: Euro (242)
- **USD**: Dollaro USA (1) 
- **GBP**: Sterlina (2)
- **JPY**: Yen giapponese (71)
- **CHF**: Franco svizzero (3)
- E altre 27 valute...

Ogni valuta ha:
- Codice ISO numerico
- Limiti min/max
- Decimali supportati

## 🎨 Gestione UI/UX

### Icone e Immagini
- **Directory**: `images/cards/`
- **Formati**: JPG, PNG, SVG
- **Carte**: Visa, Mastercard, Amex, JCB, Maestro, Postepay
- **Loghi**: MyBank, PayPal BNPL

### JavaScript
- **MyBank**: `lib/gestpay-mybank.js`
- **FancyBox**: Per popup e modali
- **Select2**: Per selezione banche

## 🔐 Sicurezza e Token

### Sistema Token
- **Salvataggio**: User meta con crittografia
- **Gestione**: Classe `Gestpay_Cards`
- **Endpoint**: `/saved-cards` per gestione account
- **Operazioni**: Elimina, imposta default

### 3D Secure
- **Classe**: `class-gestpay-3DS2.php`
- **Supporto**: 3D Secure 2.0
- **Validazione**: Browser e device fingerprinting

## ⚙️ Configurazione

### Impostazioni Base
- **Abilitazione**: Enable/Disable per ogni metodo
- **Titolo**: Personalizzabile per checkout
- **Descrizione**: Testo informativo
- **Icone**: Override icone carte

### Impostazioni Specifiche
- **MyBank**: Selezione banca obbligatoria desktop
- **Consel**: Merchant ID e codice convenzione
- **PayPal**: Descrizione accordo fatturazione

## 🔄 Integrazione WooCommerce

### Hook e Filtri
- `woocommerce_payment_gateways`: Registrazione gateway
- `woocommerce_available_payment_gateways`: Disponibilità
- `gestpay_encrypt_parameters`: Parametri aggiuntivi
- `woocommerce_scheduled_subscription_payment_*`: Abbonamenti

### Endpoint Personalizzati
- `/saved-cards`: Gestione carte salvate
- **Template**: `inc/my-cards.php`

## 📱 Responsive Design

### MyBank Mobile
- Selezione banca sempre obbligatoria
- UI ottimizzata per touch
- Ricerca intelligente banche

### Generale
- Icone responsive
- Form di pagamento adattivi
- Supporto per tutti i dispositivi

## 🚀 Funzionalità Avanzate

### Server-to-Server (S2S)
- **Classe**: `class-gestpay-s2s.php`
- **Operazioni**: Settle, Delete, Refund
- **Sicurezza**: Autenticazione API

### Abbonamenti
- **Classe**: `class-gestpay-subscriptions.php`
- **Supporto**: WooCommerce Subscriptions
- **Rinnovi**: Automatici con token salvati

### iFrame
- **Classe**: `class-gestpay-iframe.php`
- **Modalità**: Pagamento embedded
- **Sicurezza**: Isolamento transazioni

## 🔍 Debug e Logging

### Sistema di Log
- **Helper**: `class-gestpay-helper.php`
- **Messaggi**: Traducibili in italiano
- **Errori**: Codici specifici GestPay

### Messaggi Utente
- **File**: `inc/translatable-strings.php`
- **232 stringhe** traducibili
- **Contesti**: Checkout, account, errori

## 📊 Statistiche Codebase

- **8 metodi di pagamento** principali
- **32 valute** supportate
- **15+ classi** specializzate
- **Modularità**: Ogni metodo è indipendente
- **Estensibilità**: Sistema a filtri e hook

## 🎯 Best Practices Implementate

1. **DRY**: Riutilizzo codice tra metodi
2. **Modularità**: Classi separate per ogni metodo
3. **Sicurezza**: Token crittografati e 3DS
4. **UX**: UI responsive e intuitiva
5. **Internazionalizzazione**: Stringhe traducibili
6. **Compatibilità**: WooCommerce 3.0+ 
<style>
    body {
        font-family: Tahoma, Arial, sans-serif;
        color: #111827;
        font-size: 14px;
        background: #ffffff;
    }

    .page {
        width: 800px;
        margin: 0 auto;
        padding: 24px;
    }

    .document-header {
        text-align: center;
        border-bottom: 2px solid #111827;
        padding-bottom: 14px;
        margin-bottom: 22px;
    }

    .document-logo {
        margin-bottom: 8px;
    }

    .document-logo img {
        height: 72px;
        max-width: 130px;
        object-fit: contain;
    }

    .document-hospital-name {
        font-size: 22px;
        font-weight: bold;
    }

    .document-hospital-info {
        margin-top: 4px;
        color: #4b5563;
        font-size: 13px;
    }

    .document-title {
        text-align: center;
        font-size: 20px;
        font-weight: bold;
        margin-bottom: 18px;
    }

    .section {
        margin-bottom: 18px;
    }

    .section-title {
        font-weight: bold;
        font-size: 16px;
        margin-bottom: 8px;
        border-bottom: 1px solid #d1d5db;
        padding-bottom: 6px;
    }

    .info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px 24px;
    }

    .label {
        color: #6b7280;
        font-size: 12px;
    }

    .value {
        font-weight: bold;
        margin-top: 2px;
        white-space: pre-line;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 18px;
    }

    th,
    td {
        border: 1px solid #d1d5db;
        padding: 8px;
        vertical-align: top;
    }

    th {
        background: #f3f4f6;
        text-align: left;
    }

    .text-center {
        text-align: center;
    }

    .text-right {
        text-align: right;
    }

    .signature-grid {
        margin-top: 60px;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 60px;
        text-align: center;
    }

    .signature-line {
        border-top: 1px solid #111827;
        padding-top: 8px;
    }

    .print-button {
        margin: 20px auto;
        width: 800px;
        text-align: right;
    }

    .print-button button {
        padding: 8px 16px;
        background: #111827;
        color: white;
        border: 0;
        border-radius: 6px;
        cursor: pointer;
    }

    @media print {
        .print-button {
            display: none;
        }

        body {
            margin: 0;
        }

        .page {
            width: auto;
            padding: 16px;
        }
    }
</style>

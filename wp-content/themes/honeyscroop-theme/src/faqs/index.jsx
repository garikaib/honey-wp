import React from 'react';
import { createRoot } from 'react-dom/client';
import FaqList from './FaqList';

const rootElement = document.getElementById('honey-faqs-root');
if (rootElement) {
    createRoot(rootElement).render(
        <React.StrictMode>
            <FaqList />
        </React.StrictMode>
    );
}

import React, { createContext, useContext, useState, useEffect } from 'react';

const AdminContext = createContext();

export const useAdmin = () => {
    const context = useContext(AdminContext);
    if (!context) {
        throw new Error('useAdmin must be used within an AdminProvider');
    }
    return context;
};

export const AdminProvider = ({ children }) => {
    const [activeTab, setActiveTab] = useState('dashboard');
    const [showAdvanced, setShowAdvanced] = useState(false);
    const [settings, setSettings] = useState(null);

    // Initial tab from URL or persistence if needed
    useEffect(() => {
        const urlParams = new URLSearchParams(window.location.search);
        const tab = urlParams.get('tab');
        if (tab) {
            setActiveTab(tab);
        }

        const savedAdvanced = localStorage.getItem('honeyAdmin_showAdvanced');
        if (savedAdvanced) {
            setShowAdvanced(JSON.parse(savedAdvanced));
        }
    }, []);

    useEffect(() => {
        localStorage.setItem('honeyAdmin_showAdvanced', JSON.stringify(showAdvanced));
    }, [showAdvanced]);

    const value = {
        activeTab,
        setActiveTab,
        showAdvanced,
        setShowAdvanced,
        settings,
        setSettings
    };

    return (
        <AdminContext.Provider value={value}>
            {children}
        </AdminContext.Provider>
    );
};

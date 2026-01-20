
// Initialize Supabase Client
// NOTE: Replace these with your actual Project URL and Anon Key from Supabase Dashboard
const SUPABASE_URL = 'https://YOUR_PROJECT_ID.supabase.co';
const SUPABASE_KEY = 'YOUR_ANON_KEY';

// Check if Supabase is loaded
let supabase;
if (typeof createClient !== 'undefined') {
    supabase = createClient(SUPABASE_URL, SUPABASE_KEY);
} else {
    console.warn('Supabase SDK not loaded. Database features will be simulated.');
}

/**
 * Submit Contact Form Data
 * @param {Object} data - { name, email, subject, message }
 */
async function submitContactForm(data) {
    if (!supabase) {
        console.log('SIMULATION: Contact form submitted', data);
        return { success: true, message: 'Message sent successfully! (Simulation)' };
    }

    try {
        const { error } = await supabase
            .from('contacts')
            .insert([
                { 
                    name: data.name,
                    email: data.email,
                    subject: data.subject,
                    message: data.message,
                    created_at: new Date().toISOString()
                }
            ]);

        if (error) throw error;
        return { success: true, message: 'Message sent successfully!' };
    } catch (error) {
        console.error('Error submitting form:', error);
        return { success: false, message: 'Failed to send message. Please try again.' };
    }
}

/**
 * Subscribe to Newsletter
 * @param {string} email 
 */
async function subscribeNewsletter(email) {
    if (!supabase) {
        console.log('SIMULATION: Newsletter subscription', email);
        return { success: true, message: 'Subscribed successfully! (Simulation)' };
    }

    try {
        const { error } = await supabase
            .from('subscribers')
            .insert([{ email: email, created_at: new Date().toISOString() }]);

        if (error) {
            if (error.code === '23505') { // Unique violation
                return { success: true, message: 'You are already subscribed!' };
            }
            throw error;
        }
        return { success: true, message: 'Subscribed successfully!' };
    } catch (error) {
        console.error('Error subscribing:', error);
        return { success: false, message: 'Failed to subscribe.' };
    }
}

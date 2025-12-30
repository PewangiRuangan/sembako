const SUPABASE_URL = 'https://tupmfmptxscnxaqbkjmi.supabase.co';
const SUPABASE_ANON_KEY = 'sb_publishable_K8aPNxYmPQ4xCY-SXHtAsw_D5Sg_Bqg';

// Initialize Supabase Client
const { createClient } = supabase;
const _supabase = createClient(SUPABASE_URL, SUPABASE_ANON_KEY);

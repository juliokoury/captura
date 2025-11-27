let currentStep = 0;
const totalSteps = 7; // 0 to 7 (0 is welcome, 7 is final question)

function updateProgressBar() {
    const progress = (currentStep / totalSteps) * 100;
    document.getElementById('progress-bar').style.width = `${progress}%`;
}

function showStep(stepIndex) {
    // Hide all steps
    document.querySelectorAll('.step-container').forEach(el => {
        el.classList.add('hidden');
        el.classList.remove('slide-enter-active');
    });

    // Show current step
    const stepElement = document.getElementById(`step-${stepIndex}`);
    if (stepElement) {
        stepElement.classList.remove('hidden');
        stepElement.classList.add('slide-enter-active');

        // Focus on input if exists
        const input = stepElement.querySelector('input');
        if (input) {
            setTimeout(() => input.focus(), 100);
        }
    }

    // Handle form visibility
    const form = document.getElementById('quiz-form');
    if (stepIndex > 0 && stepIndex <= 7) {
        form.classList.remove('hidden');
    } else if (stepIndex === 0) {
        form.classList.add('hidden');
    }

    updateProgressBar();
}

function nextStep() {
    // Validation for current step
    if (currentStep > 0) {
        const currentStepEl = document.getElementById(`step-${currentStep}`);
        const input = currentStepEl.querySelector('input');
        if (input && input.hasAttribute('required') && !input.value.trim()) {
            input.classList.add('border-red-500');
            input.placeholder = "Este campo é obrigatório";
            return;
        }
        if (input) {
            input.classList.remove('border-red-500');
        }
    }

    if (currentStep < totalSteps) {
        currentStep++;
        showStep(currentStep);
    }
}

function submitForm(interestValue) {
    // Show loading
    document.getElementById('quiz-form').classList.add('hidden');
    document.getElementById('loading-screen').classList.remove('hidden');

    // Collect data
    const formData = new FormData(document.getElementById('quiz-form'));
    const data = Object.fromEntries(formData.entries());
    data.interesse = interestValue; // Add the final question answer manually since it's a button

    // Send to API
    fetch('api/new-lead.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
        .then(async response => {
            const text = await response.text();
            try {
                return JSON.parse(text);
            } catch (e) {
                throw new Error('Resposta inválida do servidor: ' + text.substring(0, 50) + '...');
            }
        })
        .then(result => {
            if (result.error) {
                throw new Error(result.error);
            }
            document.getElementById('loading-screen').classList.add('hidden');
            document.getElementById('success-screen').classList.remove('hidden');
            console.log('Success:', result);
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erro: ' + error.message);
            document.getElementById('loading-screen').classList.add('hidden');
            document.getElementById('quiz-form').classList.remove('hidden');
        });
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    showStep(0);

    // Allow Enter key to go to next step
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
            const activeStep = document.getElementById(`step-${currentStep}`);
            if (activeStep && !activeStep.classList.contains('hidden')) {
                // Check if we are on the final step (buttons), enter shouldn't trigger nextStep blindly there
                if (currentStep < 7 && currentStep > 0) {
                    nextStep();
                }
            }
        }
    });
});

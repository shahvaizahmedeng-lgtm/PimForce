@props(['currentStep' => 1, 'steps' => []])

<div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 2rem; padding: 0 1rem;">
    @foreach($steps as $index => $step)
        @php
            $stepNumber = $index + 1;
            $isActive = $stepNumber === $currentStep;
            $isCompleted = $stepNumber < $currentStep;
        @endphp
        
        <div style="display: flex; flex-direction: column; align-items: center; flex: 1;">
            <!-- Step Circle -->
            <div style="
                width: 2.5rem; 
                height: 2.5rem; 
                border-radius: 50%; 
                display: flex; 
                align-items: center; 
                justify-content: center; 
                margin-bottom: 0.5rem;
                @if($isActive)
                    background-color: #9333ea; 
                    color: white;
                @elseif($isCompleted)
                    background-color: white; 
                    color: #9333ea; 
                    border: 2px solid #9333ea;
                @else
                    background-color: #e5e7eb; 
                    color: #6b7280;
                @endif
            ">
                @if($isCompleted)
                    <!-- Checkmark for completed steps -->
                    <svg style="width: 1.25rem; height: 1.25rem;" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                @else
                    {{ $stepNumber }}
                @endif
            </div>
            
            <!-- Step Label -->
            <span style="
                font-size: 0.875rem; 
                font-weight: 500; 
                text-align: center;
                @if($isActive)
                    color: #9333ea;
                @elseif($isCompleted)
                    color: #9333ea;
                @else
                    color: #6b7280;
                @endif
            ">
                {{ $step }}
            </span>
        </div>
        
        @if($index < count($steps) - 1)
            <!-- Connector Line -->
            <div style="
                flex: 1; 
                height: 2px; 
                background-color: {{ $isCompleted ? '#9333ea' : '#e5e7eb' }};
                margin: 0 0.5rem;
                margin-top: 1.25rem;
            "></div>
        @endif
    @endforeach
</div>

import React from 'react';
import { cn } from '@/lib/utils';
import { Check } from 'lucide-react';

const Checkbox = React.forwardRef(({ className, checked, onCheckedChange, ...props }, ref) => {
  return (
    <button
      type="button"
      role="checkbox"
      aria-checked={checked}
      onClick={() => onCheckedChange?.(!checked)}
      className={cn(
        "h-5 w-5 rounded border-2 border-gray-600 bg-gray-700 flex items-center justify-center transition-all duration-200 hover:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:ring-offset-2 focus:ring-offset-gray-900 cursor-pointer",
        checked && "bg-violet-600 border-violet-600",
        className
      )}
      ref={ref}
      {...props}
    >
      {checked && (
        <Check className="h-3 w-3 text-white" strokeWidth={3} />
      )}
    </button>
  );
});

Checkbox.displayName = "Checkbox";

export { Checkbox };
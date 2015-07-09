% -----------------------------------------------------------------------
% swp.cls

\NeedsTeXFormat{LaTeX2e}

\LoadClass[11pt,a4paper,twoside]{article}
\usepackage{ngerman}
\usepackage[latin1]{inputenc}
\usepackage{graphicx}
\usepackage{moreverb}
\usepackage{fancyhdr}

% -----------------------------------------------------------------------
% Page layout

% enlarge page
\setlength{\textwidth}{16cm}
\setlength{\topmargin}{-1cm}
\setlength{\evensidemargin}{0cm}
\setlength{\oddsidemargin}{0cm}
\setlength{\textheight}{24cm}

% skip between paragraphs
\setlength{\parskip}{1ex}
% ... and no indentation at start of a new paragraph
\setlength{\parindent}{0ex}

\newcommand{\thissemester}{SS 2015}
\newcommand{\thistitle}{Softwaretechnikpraktikum}

\pagestyle{fancy}
\thispagestyle{empty}

% -----------------------------------------------------------------------
% Main macros
%
% here the title is defined.
% Parameters:
% #1: Titel 
% #2: Erstellungsdatum
% #3: Autor 


\renewcommand{\maketitle}[3]
          {\@maketitle{#1}{#2}{#3}}
\newcommand\skipsection{\stepcounter{section}}

%  -----------------------------------------------------------------------

\renewcommand{\@maketitle}[9]
{
    \begin{tabular}[t]{lr}
      \begin{tabular}[t]{l}
        \begin{tabular}[t]{l@{\hskip 10mm}l}
            Softwaretechnikpraktikum & Gruppe: swp15-aae \hspace{4ex} \\
            \thissemester  &  Betreuer: Prof. Gräbe \\
            Datum: {#3}	&	Tutor: Klemens Schölhorn\\
        \end{tabular} \vspace{20mm} \\
        {\Huge\sf {#2}} \\[1.5ex]
      \end{tabular}
      &
      \begin{tabular}[t]{l}
     	  Projektteam:\\
          Felix Albroscheit\\
          Dorian Dahms\\
          Paul Eisenhuth\\
          Martin Lechner\\
          Christian Seidemann\\
          Ruth von Borell\\
          Franz Wendt\\
      \end{tabular} \\[2ex]
    \end{tabular}\\
    \rule{\linewidth}{1pt}

% -----------------------------------------------------------------------
% Create headings (Thanks to Elmar Loos)

    \fancyhead{} %clear all fields
    \fancyhead[LO,RE]{swp15-aae}
    \fancyhead[RO,LE]{{#4}, {#3}}
    \fancyfoot{} %clear all fields
    \fancyfoot[RO,LE]{\thepage}
}

\endinput
